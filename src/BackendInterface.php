<?php

namespace Krisseck\PhpRag;

interface BackendInterface {

    /**
     * Indexes a piece of content.
     *
     * @param $id string|integer
     * @param $content string
     * @return boolean
     */
    public function indexContent($id, $content);

    /**
     * Clears the index of all content.
     *
     * @return boolean
     */
    public function clearIndex();

    /**
     * Searches the backend and returns array of documents
     *
     * @param $prompt string User's prompt
     * @param $amount integer Amount of results to return
     * @return array
     */
    public function search($prompt, $amount);
}