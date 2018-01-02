<?php

class RBNode
{
    const COLOR_BLACK = 'BLK';
    const COLOR_RED = 'RED';
    const POS_LEFT = 'LFT';
    const POS_RIGHT = 'RHT';

    /**
     * @var RBNode $left ;
     */
    private $left;

    /**
     * @var RBNode $right ;
     */
    private $right;

    /**
     * @var RBNode $parent ;
     */
    public $parent;

    /**
     * 结点值
     * @var $value
     */
    public $value;

    /**
     * 颜色
     * @var $color
     */
    public $color;

    public function __construct($value, $color)
    {
        $this->setValue($value);
        $this->setColor($color);
    }

    /**
     * 在结点上添加一个子结点
     *
     * @param RBNode $child
     * @param $pos
     */
    public function append(RBNode $child, $pos)
    {
        $child->setParent($this);
        if ($pos == RBNode::POS_RIGHT) {
            $this->setRight($child);
        } else {
            $this->setLeft($child);
        }
    }

    /**
     * 获取兄弟结点
     * @return null|RBNode
     */
    public function getBro()
    {
        if ($this->getParent() && $this->getParent()->getLeft() && $this->getValue() == $this->getParent()->getLeft()->getValue()) {
            return $this->getParent()->getRight();
        } elseif ($this->getParent() && $this->getParent()->getRight() && $this->getValue() == $this->getParent()->getRight()->getValue()) {
            return $this->getParent()->getLeft();
        } else {
            return null;
        }

    }

    /**
     * 获取结点所在子树的方向
     * @return string
     */
    public function getPos()
    {
        if (!$this->getParent()) {
            return '';
        }

        if ($this->getParent()->getLeft() && $this->getValue() == $this->getParent()->getLeft()->getValue()) {
            return self::POS_LEFT;
        } else {
            return self::POS_RIGHT;
        }
    }

    /**
     * 获取下一个结点
     * @return $this|RBNode
     */
    public function getNext()
    {
        if ($this->getRight()) {
            return $this->getRight()->getNext();
        }

        return $this;
    }

    /**
     * 获取上一个结点
     * @return $this|RBNode
     */
    public function getPre()
    {
        if ($this->getLeft()) {
            return $this->getLeft()->getPre();
        }

        return $this;
    }

    /**
     * 当前结点是否大于参数结点
     *
     * @param RBNode $node
     *
     * @return bool
     */
    public function largerThan(RBNode $node)
    {
        return $this->getValue() > $node->getValue();
    }

    /**
     * 当前结点是否与参数结点相等
     *
     * @param RBNode $node
     *
     * @return bool
     */
    public function isEqual(RBNode $node)
    {
        return $this->getValue() == $node->getValue();
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($node)
    {
        $this->parent = $node;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function setLeft($node)
    {
        $this->left = $node;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function setRight($node)
    {
        $this->right = $node;
    }

}

class RBTree
{

    /**
     * @var RBNode 根结点
     */
    public $root;

    /**
     * @var array 树的层级信息
     */
    public $data;

    /**
     * @var bool 调试参数
     */
    public $debug = false;


    public function __construct($num)
    {
        $this->root = new RBNode($num, RBNode::COLOR_BLACK);
    }

    /**
     * 设置一个标志值，便于调度DEBUG
     *
     * @param $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * 查询结点
     *
     * @param RBNode $node
     * @param $num
     *
     * @return null|RBNode
     */
    public function query(RBNode $node, $num)
    {
        $node = $this->searchLoc($node, $num);
        if ($node->getValue() == $num) {
            return $node;
        } else {
            return null;
        }
    }

    /**
     * 按照层级打印树结点的 值|颜色|方向
     */
    public function printTree()
    {
        $this->data = array();
        $this->getChild($this->root, 0);
        $total = count($this->data);

        for ($i = 1; $i <= $total; $i++) {
            foreach ($this->data[$i] as $node) {
                echo $node . ' ';
            }
            echo PHP_EOL;
        }
    }

    /**
     * 插入一个元素值
     *
     * @param $num
     *
     * @return bool
     */
    public function insert($num)
    {
        $parent = $this->searchLoc($this->root, $num);
        if ($parent->getValue() == $num) {
            return false;
        }

        $new = new RBNode($num, RBNode::COLOR_RED);
        // 将子结点连接到父结点上；
        if ($parent->largerThan($new)) {
            $parent->append($new, RBNode::POS_LEFT);
        } else {
            $parent->append($new, RBNode::POS_RIGHT);
        }
        $this->repairInsert($new);
        return true;
    }

    /**
     * 删除一个结点
     *
     * @param $num
     *
     * @return bool
     */
    public function delete($num)
    {
        $node = $this->searchLoc($this->root, $num);
        if ($node->getValue() != $num) {
            return false;
        }

        // 找到比删除结点大的最小结点，用来替换，没有子结点时自身就是替代品
        if ($node->getRight()) {
            $replacement = $node->getRight()->getPre();
        } elseif ($node->getLeft()) {
            $replacement = $node->getLeft()->getNext();
        } else {
            $replacement = $node;
        }

        // 交换值
        $tmp_value = $node->getValue();
        $node->setValue($replacement->getValue());
        $replacement->setValue($tmp_value);

        $this->repairDelete($replacement);

        // 修复后删除此替代结点
        if ($replacement->getPos() == RBNode::POS_LEFT) {
            $replacement->getParent()->setLeft(null);
        } else {
            $replacement->getParent()->setRight(null);
        }
        $replacement = null;
        return true;
    }

    /**
     * 处理节点插入的情况
     *
     * @param RBNode $node
     */
    private function repairInsert(RBNode $node)
    {
        $parent = $node->getParent();
        // 如果是root结点或父结点是黑色，直接返回成功
        if (!$parent || $parent->getColor() == RBNode::COLOR_BLACK) {
            $this->root->setColor(RBNode::COLOR_BLACK);
            return;
        }

        // 以下父结点为红色
        // 没有叔结点（叔结点为黑色Nil）或叔结点为黑色时，树旋转一下即可达到平衡
        if (!$parent->getBro() || $parent->getBro()->getColor() == RBNode::COLOR_BLACK) {
            // 当新结点、父结点、祖父结点在同一条线上
            if ($node->getPos() == $parent->getPos()) {
                if ($parent->getPos() == RBNode::POS_LEFT) {
                    $this->rotate($parent, RBNode::POS_RIGHT);
                } else {
                    $this->rotate($parent, RBNode::POS_LEFT);
                }

                // 此时新插入结点是子树的父结点
                $parent->setColor(RBNode::COLOR_BLACK);
                $parent->getLeft()->setColor(RBNode::COLOR_RED);
                $parent->getRight()->setColor(RBNode::COLOR_RED);
            } else {
                // 当新结点、父结点、祖父结点不在同一条线上
                $parent_pos = $parent->getPos();
                $new_pos = $node->getPos();
                $this->rotate($node, $parent_pos);
                $this->rotate($node, $new_pos);

                // 此时新插入结点是子树的叶子结点
                $node->setColor(RBNode::COLOR_BLACK);
                $node->getLeft()->setColor(RBNode::COLOR_RED);
                $node->getRight()->setColor(RBNode::COLOR_RED);
            }
            return;
        }

        // 父结点和叔结点都是红色时，将父叔变黑，祖父变红，再递归处理祖父和其父亲的情况
        if ($parent->getBro()->getColor() == RBNode::COLOR_RED) {
            $parent->getBro()->setColor(RBNode::COLOR_BLACK);
            $parent->setColor(RBNode::COLOR_BLACK);
            $parent->getParent()->setColor(RBNode::COLOR_RED);

            // 如果当前结点被修改为红色后父结点是黑色，则已达到平稳，不用再向上递归
            $adjust_node = $parent->getParent();
            $this->repairInsert($adjust_node);
        }
    }

    /**
     * 从结点内查找离某个值最近的位置
     *
     * @param RBNode $node
     * @param $num
     *
     * @return RBNode
     */
    private function searchLoc(RBNode $node, $num)
    {
        if ($node->getValue() > $num && $node->getLeft()) {
            return $this->searchLoc($node->getLeft(), $num);
        } elseif ($node->getValue() < $num && $node->getRight()) {
            return $this->searchLoc($node->getRight(), $num);
        } else {
            return $node;
        }
    }

    /**
     * 执行树的旋转
     *
     * @param RBNode $node
     * @param $direction
     */
    private function rotate(RBNode $node, $direction)
    {
        // 留下父结点的指针
        $ori_parent = $node->getParent();
        if (!$ori_parent) {
            return;
        }
        // 如果是一颗子树，先把子树挂在原位置上
        if ($ori_parent->getParent()) {
            $ori_parent->getParent()->append($node, $ori_parent->getPos());
        }

        if ($direction == RBNode::POS_RIGHT) {
            if ($node->getRight()) {
                $ori_parent->append($node->getRight(), RBNode::POS_LEFT);
            } else {
                $ori_parent->setLeft(null);
            }
        } elseif ($direction == RBNode::POS_LEFT) {
            if ($node->getLeft()) {
                $ori_parent->append($node->getLeft(), RBNode::POS_RIGHT);
            } else {
                $ori_parent->setRight(null);
            }
        }
        if ($ori_parent->isEqual($this->root)) {
            $this->root = $node;
            $this->root->setParent(null);
        }

        $node->append($ori_parent, $direction);
    }

    /**
     * 修复层级降低的情况
     *
     * @param RBNode $node
     */
    private function repairDelete(RBNode $node)
    {
        // 是root结点或替代结点为红色时，直接返回成功
        if ($node->isEqual($this->root) || $node->getColor() == RBNode::COLOR_RED) {
            return;
        }

        // *以下替代结点为黑色
        // 结点是黑色，肯定有兄弟结点
        $bro = $node->getBro();
        $parent = $node->getParent();

        // 兄弟结点是红色，则肯定有两个黑色的侄子结点
        if ($bro->getColor() == RBNode::COLOR_RED) {
            // 向删除结点的方向旋转
            $this->rotate($bro, $node->getPos());
            // 旋转后变色，原兄弟结点成为祖父结点，设置黑色，原祖父结点成为待删除结点的父结点，设置为红色
            $bro->setColor(RBNode::COLOR_BLACK);
            $parent->setColor(RBNode::COLOR_RED);
            // 继续处理删除情况
            $this->repairDelete($node);
            return;
        }

        // **以下替代结点为黑色，兄弟结点为黑色
        if ($bro->getLeft() != null && $bro->getLeft()->getColor() == RBNode::COLOR_RED) {
            $nephew = $bro->getLeft();
        } else if ($bro->getRight() != null && $bro->getRight()->getColor() == RBNode::COLOR_RED) {
            $nephew = $bro->getRight();
        } else {
            // 没有侄子结点时，父红，子两黑，将父变黑，另一子变红即可
            if ($parent->getColor() == RBNode::COLOR_RED) {
                $node->getParent()->setColor(RBNode::COLOR_BLACK);
                $node->getBro()->setColor(RBNode::COLOR_RED);
                return;
            }

            // 没有侄子结点，父兄弟都为黑，子树需要降层
            $node->getBro()->setColor(RBNode::COLOR_RED);
            $this->repairDelete($parent);
            return;
        }

        // 兄弟结点是黑色，如果有侄子结点一定是红色，父黑兄弟黑侄子红
        // ***以下替代结点、兄弟、父亲结点都为黑色，有红色侄子结点
        $ori_parent_color = $parent->getColor();
        $ori_bro_color = $bro->getColor();
        // 如添加一样，如果有侄子结点，先把侄子结点旋转到删除结点方向
        if ($bro->getPos() == $nephew->getPos()) {
            $this->rotate($bro, $node->getPos());
        } else {
            $this->rotate($nephew, $bro->getPos());
            $this->rotate($nephew, $node->getPos());
        }
        // 旋转后，删除原node结点(或其子结点)就对删除后对树的平衡没有影响了

        // 保持原结构的颜色
        $node->getParent()->setColor($ori_bro_color);
        $node->getParent()->getBro()->setColor($ori_bro_color);
        $node->getParent()->getParent()->setColor($ori_parent_color);
    }

    /**
     * 获取树的层级结构信息
     *
     * @param RBNode $node
     * @param $level
     */
    private function getChild(RBNode $node, $level)
    {
        $level++;
        if ($node->getLeft()) {
            $this->getChild($node->getLeft(), $level);
        }

        if ($node->getRight()) {
            $this->getChild($node->getRight(), $level);
        }

        $this->data[$level][] = $node->getValue() . '|' . $node->getColor() . '|' . $node->getPos();
    }

}

function test()
{
    $tree = new RBTree(50);
    for ($i = 0; $i < 20; $i++) {
        $value = rand(1, 100);
        $res = $tree->insert($value);
        if ($res) {
            var_dump($value);
        }
    }

    $tree->printTree();
    for ($i = 0; $i < 20; $i++) {
        $value = rand(1, 100);
        $res = $tree->delete($value);
        if ($res) {
            var_dump($value);
        }
    }

    $tree->printTree();
}

test();