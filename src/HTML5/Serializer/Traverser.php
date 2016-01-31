<?php

namespace Masterminds\HTML5\Serializer;

use Masterminds\HTML5\Interfaces\RulesInterface;

/**
 * Traverser for walking a DOM tree.
 *
 * This is a concrete traverser designed to convert a DOM tree into an HTML5 document. It is not intended to be a
 * generic DOMTreeWalker implementation.
 *
 * @see http://www.w3.org/TR/2012/CR-html5-20121217/syntax.html#serializing-html-fragments
 */
class Traverser
{

    /**
     * Namespaces that should be treated as "local" to HTML5.
     */
    private static $localNs = array(
        'http://www.w3.org/1999/xhtml' => 'html',
        'http://www.w3.org/1998/Math/MathML' => 'math',
        'http://www.w3.org/2000/svg' => 'svg'
    );

    /**
     * @var \DOMNode|\DOMNodeList
     */
    protected $dom;
    /**
     * @var resource
     */
    protected $out;
    /**
     * @var RulesInterface
     */
    protected $rules;
    /**
     * @var array
     */
    protected $options;

    /**
     * Creates a traverser.
     *
     * @param \DOMNode|\DOMNodeList $dom     The document or node to traverse.
     * @param resource              $out     A stream that allows writing. The traverser will output into this stream.
     * @param RulesInterface        $rules
     * @param array                 $options An array or options for the traverser as key/value pairs. These include:
     *                                       - encode_entities: A bool to specify if full encding should happen for all
     *                                       named character references. Defaults to false which escapes &'<>".
     *                                       - output_rules: The path to the class handling the output rules.
     */
    public function __construct($dom, $out, RulesInterface $rules, array $options = array())
    {
        $this->dom = $dom;
        $this->out = $out;
        $this->rules = $rules;
        $this->options = $options;

        $this->rules->setTraverser($this);
    }

    /**
     * Tell the traverser to walk the DOM.
     *
     * @return resource $out Returns the output stream.
     */
    public function walk()
    {
        if ($this->dom instanceof \DOMDocument) {
            $this->rules->document($this->dom);
        } elseif ($this->dom instanceof \DOMDocumentFragment) {
            // Document fragments are a special case. Only the children need to
            // be serialized.
            if ($this->dom->hasChildNodes()) {
                $this->children($this->dom->childNodes);
            }
        } elseif ($this->dom instanceof \DOMNodeList) {
            // If NodeList, loop
            // If this is a NodeList of DOMDocuments this will not work.
            $this->children($this->dom);
        } else {
            // Else assume this is a DOMNode-like data structure.
            $this->node($this->dom);
        }

        return $this->out;
    }

    /**
     * Process a node in the DOM.
     *
     * @link http://php.net/manual/en/dom.constants.php Listing of constants
     *
     * @param mixed $node A node implementing \DOMNode.
     */
    public function node($node)
    {
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:
                $this->rules->element($node);
                break;
            case XML_TEXT_NODE:
                $this->rules->text($node);
                break;
            case XML_CDATA_SECTION_NODE:
                $this->rules->cdata($node);
                break;
            case XML_PI_NODE:
                $this->rules->processorInstruction($node);
                break;
            case XML_COMMENT_NODE:
                $this->rules->comment($node);
                break;
            // Currently we don't support embedding DTDs.
            default:
                //print '<!-- Skipped -->';
                break;
        }
    }

    /**
     * Walk through all the nodes on a node list.
     *
     * @param \DOMNodeList $nodeList A list of child elements to walk through.
     */
    public function children($nodeList)
    {
        foreach ($nodeList as $node) {
            $this->node($node);
        }
    }

    /**
     * Is an element local?
     *
     * @param \DOMNode $element An element that implement \DOMNode.
     *
     * @return bool True if local and false otherwise.
     */
    public function isLocalElement(\DOMNode $element)
    {
        $namespaceURI = $element->namespaceURI;

        if ($namespaceURI === '') {
            return false;
        }

        return array_key_exists($namespaceURI, static::$localNs);
    }
}
