package trie

import "fmt"

type Node struct {
	depth    int
	children map[int32]Node
}

func InitRoot() Node {
	root := Node{}
	root.depth = 0
	root.children = make(map[int32]Node)

	return root
}

func AddKeyword(root Node, keyword string) {
	words := []int32(keyword)
	words = append(words, 0)
	insert(root, words)
}

func Match(root Node, msg string) []string {
	words := []int32(msg)
	keywords := []string{}
	if len(words) <= 0 {
		return keywords
	}

	var res_str string
	var result []int32
	for {
		result := result[:0]
		res_str = ""

		// result 会在append时重新分配地址，这里将源地址传入使用
		if query(root, words, &result) {
			for _, unicode := range result {
				res_str += fmt.Sprintf("%c", unicode) // 将unicode转化为中文
			}
			keywords = append(keywords, res_str)
		}

		if len(words) <= 1 {
			break
		}
		words = words[1:]
	}

	return keywords
}

func insert(node Node, words []int32) {
	if len(words) == 0 {
		return
	}
	// 取出slice的第一个元素
	word := words[0]
	if _, exist := node.children[word]; !exist {
		// 实例化一个node
		child := Node{}
		child.depth = node.depth + 1
		child.children = make(map[int32]Node)
		node.children[word] = child
	}

	words = words[1:]
	insert(node.children[word], words)
}

func query(node Node, words []int32, result *[]int32) bool {
	word := words[0]
	words = words[1:]
	if _, exist := node.children[word]; exist {
		*result = append(*result, word)
		if _, keyword_end := node.children[word].children[0]; keyword_end {
			return true
		}
		if len(words) <= 0 {
			return false
		}
		return query(node.children[word], words, result)
	} else {
		return false
	}
}
