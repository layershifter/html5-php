<?php

namespace Masterminds\HTML5\Interfaces;

use Masterminds\HTML5\Serializer\Traverser;

/**
 * The interface definition for Rules to generate output.
 *
 * To create a new rule set for writing output the RulesInterface needs to be implemented.
 * The resulting class can be specified in the options with the key of rules.
 *
 * For an example implementation see \Masterminds\HTML5\Serializer\OutputRules.
 */
interface RulesInterface
{

    /**
     * The class constructor.
     *
     * Note, before the rules can be used a traverser must be registered.
     *
     * @param mixed $output  The output stream to write output to.
     * @param array $options An array of options.
     */
    public function __construct($output, array $options = array());

    /**
     * Register the traverser used in but the rules.
     *
     * Note, only one traverser can be used by the rules.
     *
     * @param Traverser $traverser The traverser used in the rules.
     *
     * @return RulesInterface $this for the current object.
     */
    public function setTraverser(Traverser $traverser);

    /**
     * Write a document element (\DOMDocument).
     *
     * Instead of returning the result write it to the output stream ($output) that was passed into the constructor.
     *
     * @param \DOMDocument $dom
     */
    public function document($dom);

    /**
     * Write an element.
     *
     * Instead of returning the result write it to the output stream ($output) that was passed into the constructor.
     *
     * @param mixed $element
     */
    public function element($element);

    /**
     * Write a text node.
     *
     * Instead of returning the result write it to the output stream ($output) that was passed into the constructor.
     *
     * @param mixed $element
     */
    public function text($element);

    /**
     * Write a CDATA node.
     *
     * Instead of returning the result write it to the output stream ($output) that was passed into the constructor.
     *
     * @param mixed $element
     */
    public function cdata($element);

    /**
     * Write a comment node.
     *
     * Instead of returning the result write it to the output stream ($output) that was passed into the constructor.
     *
     * @param mixed $element
     */
    public function comment($element);

    /**
     * Write a processor instruction.
     *
     * To learn about processor instructions see \Masterminds\HTML5\InstructionProcessor
     *
     * Instead of returning the result write it to the output stream ($output) that was passed into the constructor.
     *
     * @param mixed $element
     */
    public function processorInstruction($element);
}
