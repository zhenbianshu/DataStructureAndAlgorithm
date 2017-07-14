<?php

class Trie {
    /**
     * node struct
     *
     * node = array(
     * val->word
     * next->array(node)/null
     * depth->int
     * )
     */
    private $root = array(
        'depth' => 0,
        'next' => array(),
    );

    private $matched = array();

    public function append($keyword) {
        $words = preg_split('/(?<!^)(?!$)/u', $keyword);
        array_push($words, '`');
        $this->insert($this->root, $words);
    }

    public function match($str) {
        $this->matched = array();
        $words = preg_split('/(?<!^)(?!$)/u', $str);

        while (count($words) > 0) {
            $matched = array();
            $res = $this->query($this->root, $words, $matched);
            if ($res) {
                $this->matched[] = implode('', $matched);
            }
            array_shift($words);
        }

        return $this->matched;
    }

    private function insert(&$node, $words) {
        if (empty($words)) {
            return;
        }
        $word = array_shift($words);
        if (isset($node['next'][$word])) {
            $this->insert($node['next'][$word], $words);
        } else {
            $tmp_node = array(
                'depth' => $node['depth'] + 1,
                'next' => array(),
            );
            $node['next'][$word] = $tmp_node;
            $this->insert($node['next'][$word], $words);
        }
    }

    private function query($node, $words, &$matched) {
        $word = array_shift($words);
        if (isset($node['next'][$word])) {
            array_push($matched, $word);
            if (isset($node['next'][$word]['next']['`'])) {
                return true;
            }
            return $this->query($node['next'][$word], $words, $matched);
        } else {
            $matched = array();
            return false;
        }
    }
}
