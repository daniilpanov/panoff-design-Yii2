<?php


namespace app\models;


use yii\base\Model;
use yii\db\Query;

class Page extends Model
{
    public $id, $name, $content, $title,
        $position, $type, $is_const,
        $reviews_vis, $subpages_switch;

    const PAGE_VIEW = "PV", MENU = "M";

    public function __construct($id, $mode = self::PAGE_VIEW, $page_type = null)
    {
        parent::__construct(["id" => $id]);

        $params = $this->colsByMode($mode, $page_type);

        $values = (new Query())
            ->select($params['cols'])->from("pages")
            ->where("id=:id", ['id' => $id]);
        if (!empty($params['where']))
            $values->andWhere($params['where']['str'], $params['where']['params']);
        $values = $values->one();

        foreach ($values as $property => $value)
        {
            $this->$property = $value;
        }
    }

    private function colsByMode($mode, $page_type = null)
    {
        $select = $where = null;

        switch ($mode)
        {
            case self::PAGE_VIEW:
                $select = "id, name, content, reviews_vis";
                break;

            case self::MENU:
                $select = "id, name, title";
                $where = ($page_type === null) ? null : ["str" => "type=:type", "params" => ["type" => $page_type]];
                break;
        }

        return ["cols" => $select, "where" => $where];
    }

    public static function initMenu($menu = "section")
    {
        $all_id = (new Query())->select("id")->from("pages")
            ->where("=", ['type' => $menu])->all();
        $models = [];

        foreach ($all_id as $item)
        {
            $models[$item['id']] = new self($item['id'], self::MENU, $menu);
        }

        return $models;
    }
}