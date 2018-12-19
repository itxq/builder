<?php
/**
 *  ==================================================================
 *        文 件 名: FormSubmit.php
 *        概    要:
 *        作    者: IT小强
 *        创建时间: 2018-12-19 17:30:12
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\tools;

use itxq\builder\FormBuilder;
use think\facade\Request;
use think\Response;

class FormSubmit
{
    const TEXT = 'text';
    const TEXTS = 'texts';
    const TEXT_AREA = 'text_area';
    const TEXT_AREAS = 'text_areas';
    const RADIO = 'radio';
    const DATE_RANGE = 'date_range';
    const SELECT = 'select';
    const CHECKBOX = 'checkbox';
    const PASSWORD = 'password';
    const HIDDEN = 'hidden';
    const JSON = 'json';
    
    public static function submit(FormBuilder $builder, string $pk = 'id', array $allowData = []) {
        $formData = self::getFormData($builder, $pk, $allowData);
        self::success('提交成功', $formData);
        //self::error('提交失败', []);
        exit();
    }
    
    /**
     * 获取表单数据，并进行处理
     * @param FormBuilder $builder - 表单构建器
     * @param string $pk - 主键名，默认为ID
     * @param array $allowField - 允许提交的字段
     * @return array - 返回表单数据
     */
    protected static function getFormData(FormBuilder $builder, string $pk = 'id', array $allowField = []) {
        $data = Request::post();
        $type = [];
        if (isset($data['build_form_type'])) {
            $type = $data['build_form_type'];
            unset($data['build_form_type']);
        }
        $data[$pk] = intval(get_sub_value($pk, $data, 0));
        if ($data[$pk] >= 1) {
            $allowField = get_sub_value('update', $allowField, []);
        } else {
            $allowField = get_sub_value('insert', $allowField, []);
        }
        $returnData = [];
        foreach ($data as $k => $v) {
            if (count($allowField) >= 1 && !in_array($k, $allowField)) {
                continue;
            }
            $returnData[$k] = self::handleData($builder, $k, $v, $type);
        }
        $returnData[$pk] = $data[$pk];
        return $returnData;
    }
    
    /**
     * 数据处理
     * @param FormBuilder $builder - 表单构建器
     * @param string $name - 字段名
     * @param $value - 字段值
     * @param array $type - 字段类型
     * @return mixed 返回处理好的数据
     */
    protected static function handleData(FormBuilder $builder, string $name, $value, array $type) {
        $type = get_sub_value($name, $type, false);
        if ($type === self::JSON) {
            $value = $builder->unSerialize($value);
        }
        return $value;
    }
    
    protected static function success($msg = '', $data = [], string $url = '', array $header = []) {
        $info = ['code' => 1, 'msg' => $msg, 'data' => $data, 'url' => $url];
        Response::create($info, 'json', 200)->header($header)->send();
        exit();
    }
    
    protected static function error($msg = '', $data = [], string $url = '', array $header = []) {
        $info = ['code' => 0, 'msg' => $msg, 'data' => $data, 'url' => $url];
        Response::create($info, 'json', 200)->header($header)->send();
        exit();
    }
}