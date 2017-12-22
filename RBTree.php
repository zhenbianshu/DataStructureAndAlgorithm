<?php

class RBNode {
    const COLOR_BLACK = 'B';
    const COLOR_RED = 'R';
    const INSERT_TYPE_LEFT = 'L';
    const INSERT_TYPE_RIGHT = 'R';
    public $color;

    /**
     * @var RBNode $left ;
     */
    public $left;

    /**
     * @var RBNode $right ;
     */
    public $right;

    /**
     * @var RBNode $parent ;
     */
    public $parent;

    public $value;

    public function __construct($value, $color) {
        $this->value = $value;
        $this->color = $color;
    }

    public function append(RBNode $child, $type) {
        $child->parent = $this;
        if ($type == RBNode::INSERT_TYPE_RIGHT) {
            $this->right = $child;
        } else {
            $this->left = $child;
        }
    }

    public function rotateRight() {
        // 留下父结点的副本
        $ori_parent = $this->parent;

        // 如果是一颗子树，先把子树挂在原位置上
        if ($ori_parent) {
            $ori_parent->parent->append($this, $ori_parent->getPos());
        }

        if ($this->right) {
            $ori_parent->append($this->right, RBNode::INSERT_TYPE_LEFT);
        }
        $this->append($ori_parent, RBNode::INSERT_TYPE_RIGHT);
    }

    public function rotateLeft() {
        // 留下父结点的副本
        $ori_parent = $this->parent;

        // 如果是一颗子树，先把子树挂在原位置上
        if ($ori_parent->parent) {
            $ori_parent->parent->append($this, $ori_parent->getPos());
        }

        if ($this->left) {
            $ori_parent->append($this->left, RBNode::INSERT_TYPE_RIGHT);
        }

        $this->append($ori_parent, RBNode::INSERT_TYPE_LEFT);
    }

    public function getBro() {
        if ($this->value == $this->parent->left->value) {
            return $this->parent->right;
        }

        return $this->parent->left;
    }

    public function getPos() {
        if ($this->parent->left && $this->value == $this->parent->left->value) {
            return self::INSERT_TYPE_RIGHT;
        }

        return self::INSERT_TYPE_LEFT;
    }

    public function setColor($color) {
        $this->color = $color;
    }
}

class RBTree {

    public $root;

    public function initTree($num) {
        $this->root = new RBNode($num, RBNode::COLOR_BLACK);
    }

    public function insert($num) {
        $new = new RBNode($num, RBNode::COLOR_RED);
        $parent = $this->searchLoc($this->root, $num);
        if ($parent->value == $num) {
            return false;
        }

        // 将子结点连接到父结点上；
        if ($parent->value > $new->value) {
            $parent->append($new, RBNode::INSERT_TYPE_LEFT);
        } else {
            $parent->append($new, RBNode::INSERT_TYPE_RIGHT);
        }

        // 如果父结点是黑色，直接返回成功
        if ($parent->color == RBNode::COLOR_BLACK) {
            return true;
        }
        // 以下是父结点是红色的情况

        // 有叔结点时，父结点和叔结点都是红色，则递归向上改颜色直到root，对应234树中的加层
        if ($parent->getBro()) {
            if ($parent->getBro()->color == RBNode::COLOR_RED) {
                while (true) {
                    $parent->getBro()->color = RBNode::COLOR_BLACK;
                    $parent->color = RBNode::COLOR_BLACK;
                    if (!$parent->parent) {
                        break;
                    }
                    $parent->parent->color = RBNode::COLOR_RED;
                    $parent = $parent->parent;
                }
            }
            return true;
        }

        // 没有叔结点时
        // 当新结点、父结点、祖父结点在同一条线上
        if ($new->getPos() == $parent->getPos()) {
            if ($new->getPos() == RBNode::INSERT_TYPE_LEFT) {
                $parent->rotateLeft();
            } else {
                $parent->rotateRight();
            }

            // 此时新插入结点是子树的父结点
            $new->setColor(RBNode::COLOR_BLACK);
            $new->left->setColor(RBNode::COLOR_RED);
            $new->right->setColor(RBNode::COLOR_RED);
        } else {  // 当新结点、父结点、祖父结点不在同一条线上
            if ($new->getPos() == RBNode::INSERT_TYPE_LEFT) {
                $new->rotateRight();
                $new->rotateLeft();
            } else {
                $new->rotateLeft();
                $new->rotateRight();
            }
            // 此时新插入结点是子树的叶子结点
            $new->getBro()->setColor(RBNode::COLOR_RED);
            $new->setColor(RBNode::COLOR_RED);
            $new->parent->setColor(RBNode::COLOR_BLACK);
        }
        return true;
    }


    public function searchLoc(RBNode $node, $num) {
        if ($node->value > $num && $node->left != null) {
            return $this->searchLoc($node->left, $num);
        } elseif ($node->value < $num && $node->right) {
            return $this->searchLoc($node->right, $num);
        } else {
            return $node;
        }
    }

    public function query(RBNode $node, $num) {
        $node = $this->searchLoc($node, $num);
        if ($node->value == $num) {
            return $node;
        } else {
            return null;
        }
    }
}