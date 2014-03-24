<?php

namespace Gini\CGI\Response {

    class JSON implements \Gini\CGI\Response
    {
        private $_content;

        function __construct($content)
        {
            $this->_content = $content;
        }

        function output()
        {
            header('Content-Type: application/json');
            if ($this->_content !== null) {
                file_put_contents('php://output', 
                    json_encode($this->_content, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . PHP_EOL);
            }
        }

        function content()
        {
            return $this->_content;
        }

    }

}
