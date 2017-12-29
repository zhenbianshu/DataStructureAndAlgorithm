import java.util.ArrayList;
import java.util.List;

public class RBTree {
    public static void main(String[] args) {
        Tree tree = new Tree(50);
        for (int i = 0; i < 20; i++) {
            double rand = Math.random();
            int num = (int) (rand * 100);
            tree.insert(num);
        }

        for (int j = 0; j < 20; j++) {
            double rand = Math.random();
            int num = (int) (rand * 100);
            tree.delete(num);
        }
        tree.printTree();
    }
}

class RBNode {
    /**
     * 节点颜色
     */
    public enum Color {
        BLK("BLK"), RED("RED");
        private String color;

        private Color(String color) {
            this.color = color;
        }

        public String getColor() {
            return this.color;
        }
    }

    /**
     * 节点位置和方向
     */
    public enum Pos {
        LFT("LET"), RGT("RGT"), TOP("TOP");
        private String pos;

        private Pos(String pos) {
            this.pos = pos;
        }

        public String getPos() {
            return this.pos;
        }
    }

    /**
     * 颜色
     */
    protected Color color;

    /**
     * 值
     */
    protected int value;

    /**
     * 父结点
     */
    protected RBNode parent;

    /**
     * 左结点
     */
    protected RBNode left;

    /**
     * 右结点
     */
    protected RBNode right;

    public RBNode(int value, Color color) {
        this.setValue(value);
        this.setColor(color);
    }

    public int getValue() {
        return this.value;
    }

    public RBNode getLeft() {
        return this.left;
    }

    public RBNode getParent() {
        return this.parent;
    }

    public RBNode getRight() {
        return this.right;
    }

    public Color getColor() {
        return this.color;
    }

    public void setColor(Color color) {
        this.color = color;
    }

    public void setValue(int value) {
        this.value = value;
    }

    public void setLeft(RBNode left) {
        this.left = left;
    }

    public void setParent(RBNode parent) {
        this.parent = parent;
    }

    public void setRight(RBNode right) {
        this.right = right;
    }

    /**
     * 获取离最近的下一个结点
     *
     * @return node
     */
    public RBNode getNext() {
        if (this.getRight() != null) {
            return this.getRight().getNext();
        }

        return this;
    }

    /**
     * 获取离最近的上一个结点
     *
     * @return node
     */
    public RBNode getPre() {
        if (this.getLeft() != null) {
            return this.getLeft().getPre();
        }

        return this;
    }

    /**
     * 获取节点在子树的位置
     *
     * @return pos
     */
    public Pos getPos() {
        if (this.getParent() == null) {
            return Pos.TOP;
        }

        if (this.getParent().getLeft() != null && this.getValue() == this.getParent().getLeft().getValue()) {
            return Pos.LFT;
        } else {
            return Pos.RGT;
        }
    }

    /**
     * 获取兄弟结点
     *
     * @return node
     */
    public RBNode getBro() {
        if (this.getParent() != null && this.getParent().getLeft() != null && this.getValue() == this.getParent().getLeft().getValue()) {
            return this.getParent().getRight();
        } else if (this.getParent() != null && this.getParent().getRight() != null && this.getValue() == this.getParent().getRight().getValue()) {
            return this.getParent().getLeft();
        } else {
            return null;
        }
    }

    /**
     * 在结点上挂一个子结点
     *
     * @param child 子结点
     * @param pos   位置
     */
    public void append(RBNode child, Pos pos) {
        child.setParent(this);
        if (pos == Pos.RGT) {
            this.setRight(child);
        } else {
            this.setLeft(child);
        }
    }

    /**
     * 判断两个结点是否相等
     *
     * @param node 结点
     * @return 结果
     */
    public boolean isEqual(RBNode node) {
        return this.getValue() == node.getValue();
    }

    /**
     * 判断结点是否大于参数结点
     *
     * @param node 结点
     * @return 结果
     */
    public boolean largerThan(RBNode node) {
        return this.getValue() > node.getValue();
    }
}

class Tree {
    protected RBNode root;
    protected boolean debug;
    protected List data;

    public Tree(int value) {
        this.root = new RBNode(value, RBNode.Color.BLK);
    }

    public void setDebug(boolean debug) {
        this.debug = debug;
    }

    public boolean getDebug() {
        return this.debug;
    }

    /**
     * 查询结点子树中数字是否存在
     *
     * @param node 结点
     * @param num  数字
     * @return 结点
     */
    public RBNode query(RBNode node, int num) {
        node = this.searchLoc(node, num);
        if (node.getValue() == num) {
            return node;
        }

        return null;
    }

    /**
     * 打印红黑树
     */
    public void printTree() {
        this.data = new ArrayList();
        this.getChild(this.root, 0);

        for (int i = 0; i < this.data.size(); i++) {
            List ele = (ArrayList) this.data.get(i);
            String[] data = (String[]) ele.toArray(new String[ele.size()]);
            for (int j = 0; j < data.length; j++) {
                System.out.print(data[j]);
                System.out.print(" ");
            }
            System.out.print("\n");
        }
    }

    /**
     * 向树中插入一个新节点
     *
     * @param num 节点值
     * @return 结果
     */
    public boolean insert(int num) {
        RBNode parent = this.searchLoc(this.root, num);
        if (parent.getValue() == num) {
            return false;
        }

        RBNode newNode = new RBNode(num, RBNode.Color.RED);

        if (parent.largerThan(newNode)) {
            parent.append(newNode, RBNode.Pos.LFT);
        } else {
            parent.append(newNode, RBNode.Pos.RGT);
        }
        return this.repairInsert(newNode);
    }

    /**
     * 从子树中删除一个结点
     *
     * @param num 数字
     * @return 结果
     */
    public boolean delete(int num) {
        RBNode node = this.searchLoc(this.root, num);
        if (node.getValue() != num) {
            return false;
        }

        // 找到比删除结点大的最小结点，用来替换，没有子结点时自身就是替代品
        RBNode replacement;
        if (node.getRight() != null) {
            replacement = node.getRight().getNext();
        } else if (node.getLeft() != null) {
            replacement = node.getLeft().getPre();
        } else {
            replacement = node;
        }

        // 交换值
        int tmp_value = node.getValue();
        node.setValue(replacement.getValue());
        replacement.setValue(tmp_value);
        // 执行完交换，此时只需要把被替换对象的删除处理掉就好了

        // 如果结点颜色是黑色，相当于子树少了一层，需要递归向上处理平衡
        if (replacement.getColor() == RBNode.Color.BLK) {
            RBNode point = replacement;
            while (point.getValue() != this.root.getValue() && !this.repairDelete(point)) {
                // 如果当前层级处理失败，就必须要降层了，父黑兄弟红时，将兄弟结点置为红色即可（由于会首先处理父红或侄子有红的情况，所以处理失败时一定是全黑）
                if (point.getParent().getColor() == RBNode.Color.BLK) {
                    point.getBro().setColor(RBNode.Color.RED);
                    point = point.getParent();
                } else {
                    // 父红，子两黑，将父变黑，另一子变红即可
                    point.getParent().setColor(RBNode.Color.BLK);
                    point.getBro().setColor(RBNode.Color.RED);
                    break;
                }
            }
        }

        if (replacement.getPos() == RBNode.Pos.LFT) {
            replacement.getParent().setLeft(null);
        } else {
            replacement.getParent().setRight(null);
        }
        return true;
    }

    /**
     * 修复删除后的红黑树属性
     *
     * @param node 节点
     * @return 结果
     */
    private boolean repairDelete(RBNode node) {
        // 结点是黑色，肯定有兄弟结点
        RBNode bro = node.getBro();
        RBNode parent = node.getParent();

        // 兄弟结点是红色，则肯定有两个黑色的侄子结点
        if (bro.getColor() == RBNode.Color.RED) {
            // 向删除结点的方向旋转
            this.rotate(bro, node.getPos());
            // 旋转后变色，原兄弟结点成为祖父结点，设置黑色，原祖父结点成为待删除结点的父结点，设置为红色
            bro.setColor(RBNode.Color.BLK);
            parent.setColor(RBNode.Color.RED);
            // 继续处理删除情况
            return this.repairDelete(node);
        }

        // 兄弟结点是黑色，如果有侄子结点一定是红色
        RBNode nephew;
        if (bro.getLeft() != null && bro.getLeft().getColor() == RBNode.Color.RED) {
            nephew = bro.getLeft();
        } else if (bro.getRight() != null && bro.getRight().getColor() == RBNode.Color.RED) {
            nephew = bro.getRight();
        } else {
            // 没有侄子结点，子树需要降层
            return false;
        }

        RBNode.Color oriParentColor = parent.getColor();
        RBNode.Color oriBroColor = bro.getColor();
        // 如添加一样，如果有侄子结点，先把侄子结点旋转到删除结点方向
        if (bro.getPos() == nephew.getPos()) {
            this.rotate(bro, node.getPos());
        } else {
            this.rotate(nephew, bro.getPos());
            this.rotate(nephew, node.getPos());
        }
        // 旋转后，删除原node结点(或其子结点)就对删除后对树的平衡没有影响了

        // 保持原结构的颜色
        node.getParent().setColor(oriBroColor);
        node.getParent().getBro().setColor(oriBroColor);
        node.getParent().getParent().setColor(oriParentColor);
        return true;
    }

    /**
     * 修复添加一个结点后的红黑树平衡
     *
     * @param node 结点
     * @return 结果
     */
    private boolean repairInsert(RBNode node) {
        // 如果父结点是黑色，直接返回成功
        RBNode parent = node.getParent();
        // 如果没有父结点说明是root结点
        if (parent == null) {
            node.setColor(RBNode.Color.BLK);
            return true;
        }
        if (parent.getColor() == RBNode.Color.BLK) {
            return true;
        }

        // 以下父结点为红色
        // 没有叔结点时，树旋转一下即可达到平衡
        if (parent.getBro() == null) {
            // 当新结点、父结点、祖父结点在同一条线上
            if (node.getPos() == parent.getPos()) {
                if (parent.getPos() == RBNode.Pos.LFT) {
                    this.rotate(parent, RBNode.Pos.RGT);
                } else {
                    this.rotate(parent, RBNode.Pos.LFT);
                }

                // 此时新插入结点是子树的父结点
                parent.setColor(RBNode.Color.BLK);
                parent.getLeft().setColor(RBNode.Color.RED);
                parent.getRight().setColor(RBNode.Color.RED);
            } else {
                // 当新结点、父结点、祖父结点不在同一条线上
                RBNode.Pos parentPos = parent.getPos();
                RBNode.Pos newPos = node.getPos();
                this.rotate(node, parentPos);
                this.rotate(node, newPos);

                // 此时新插入结点是子树的叶子结点
                node.setColor(RBNode.Color.BLK);
                node.getLeft().setColor(RBNode.Color.RED);
                node.getRight().setColor(RBNode.Color.RED);
            }
            return true;
        }

        // 父结点和叔结点都是红色时，将父叔变黑，祖父变红，再递归处理祖父和其父亲的情况
        if (parent.getBro().getColor() == RBNode.Color.RED) {
            parent.getBro().setColor(RBNode.Color.BLK);
            parent.setColor(RBNode.Color.BLK);
            parent.getParent().setColor(RBNode.Color.RED);

            // 如果当前结点被修改为红色后父结点是黑色，则已达到平稳，不用再向上递归
            RBNode adjustNode = parent.getParent();
            return this.repairInsert(adjustNode);
        } else {
            // 父结点红，叔结点是黑色时，处于中间模式，此时把加层的子树旋转到对边子树达到平衡
            if (node.getPos() == parent.getPos()) {
                this.rotate(parent, parent.getBro().getPos());

                // 此时调整结点是子树的父结点
                parent.setColor(RBNode.Color.BLK);
                parent.getLeft().setColor(RBNode.Color.RED);
                parent.getRight().setColor(RBNode.Color.RED);
            } else {
                RBNode.Pos oriPos = node.getPos();
                this.rotate(node, parent.getPos());
                this.rotate(node, oriPos);

                // 此时调整结点是子树的父结点
                node.setColor(RBNode.Color.BLK);
                node.getLeft().setColor(RBNode.Color.RED);
                node.getRight().setColor(RBNode.Color.RED);
            }
        }

        this.root.setColor(RBNode.Color.BLK);
        return true;
    }

    /**
     * 旋转节点
     *
     * @param node 结点
     * @param pos  位置
     */
    private void rotate(RBNode node, RBNode.Pos pos) {
        // 留下父结点的指针
        RBNode oriParent = node.getParent();
        if (oriParent == null) {
            return;
        }
        // 如果是一颗子树，先把子树挂在原位置上
        if (oriParent.getParent() != null) {
            oriParent.getParent().append(node, oriParent.getPos());
        }

        if (pos == RBNode.Pos.RGT) {
            if (node.getRight() != null) {
                oriParent.append(node.getRight(), RBNode.Pos.LFT);
            } else {
                oriParent.setLeft(null);
            }
        } else if (pos == RBNode.Pos.LFT) {
            if (node.getLeft() != null) {
                oriParent.append(node.getLeft(), RBNode.Pos.RGT);
            } else {
                oriParent.setRight(null);
            }
        }
        if (oriParent.isEqual(this.root)) {
            this.root = node;
            this.root.setParent(null);
        }

        node.append(oriParent, pos);
    }

    /**
     * 从结点内查找离某个值最近的位置
     *
     * @param node 节点
     * @param num  数字
     * @return RBNode
     */
    private RBNode searchLoc(RBNode node, int num) {
        if (node.getValue() > num && node.getLeft() != null) {
            return this.searchLoc(node.getLeft(), num);
        } else if (node.getValue() < num && node.getRight() != null) {
            return this.searchLoc(node.getRight(), num);
        }

        return node;
    }

    /**
     * 获取子树的结点属性
     *
     * @param node  结点
     * @param depth 预定深度
     */
    private void getChild(RBNode node, int depth) {
        String nodeInfo = node.getValue() + "|" + node.getColor() + "|" + node.getPos();

        int treeDepth = this.data.size();
        if (depth >= treeDepth || depth == 0) {
            List levelData = new ArrayList();
            this.data.add(levelData);
        }
        List list = (ArrayList) this.data.get(depth);
        list.add(nodeInfo);

        depth++;
        if (node.getLeft() != null) {
            this.getChild(node.getLeft(), depth);
        }

        if (node.getRight() != null) {
            this.getChild(node.getRight(), depth);
        }
    }
}