<?php

class Trie
{
	/**
	 * node struct
	 * node = array(
	 * val->word
	 * next->array(node)/null
	 * depth->int
	 * )
	 */
	public $root = [
		'depth'  => 0,
		'next'   => [],
		'value'  => '',
		'is_end' => true,
	];

	public $matched = [];

	public function append ($keyword)
	{
		$words = preg_split('/(?<!^)(?!$)/u', $keyword);
		$this->insert($this->root, $words);
	}

	public function match ($str)
	{
		$this->matched = [];
		$words         = preg_split('/(?<!^)(?!$)/u', $str);

		while (count($words) > 0) {
			$this->query($this->root, $words);
			array_shift($words);
		}

		return $this->matched;

	}

	private function insert (&$node, $words)
	{
		if (empty($words)) {
			return;
		}
		$word = array_shift($words);
		if (isset($node['next'][ $word ])) {
			$this->insert($node['next'][ $word ], $words);
		} else {
			$tmp_node = [
				'depth'  => $node['depth'] + 1,
				'next'   => [],
				'value'  => $node['value'] . $word,
				'is_end' => count($words) === 0 ? true : false,
			];

			$node['next'][ $word ] = $tmp_node;
			$this->insert($node['next'][ $word ], $words);
		}
	}

	private function query ($node, $words)
	{
		$word = array_shift($words);

		if (isset($node['next'][ $word ])) {
			if (isset($node['next'][ $word ]['next']['`'])) {
				array_push($this->matched, $node['next'][ $word ]['value']);
				$this->query($node['next'][ $word ], $words);
			}
		}

		return;
	}
}

$trie = new Trie();
$trie->append('广');
$trie->append('广州');
$trie->match('广州市');
