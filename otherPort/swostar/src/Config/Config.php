<?php

namespace SwoStar\Config;

class Config
{
    protected $items = [];

    protected $configPath = '';

    public function __construct()
    {
        $this->configPath = app()->getBasePath() . '/config';
        // 读取配置
        $this->items = $this->phpParser();
//        dd($this->items);
    }

    /**
     * .php 后缀的配置
     * @return null
     */
    public function phpParser()
    {
        // 此处跳过多级情况
        $files = scandir($this->configPath);
        $data = null;
        // 读取文件
        foreach ($files as $key => $file) {
            if ($file === '' || $file === '..' || $file === '.') {
                // 多级情况考虑  递归去解
                continue;
            }
            // 2.1 获取文件名
            $filename = stristr($file, '.php', true);
            $data[$filename] = include $this->configPath . '/' . $file;
        }
        return $data;
    }

    public function get($key)
    {
        $data = $this->items;
        $keys = explode('.', $key);
        foreach ($keys as $key => $value) {
            $data = $data[$value];
        }
        return $data;
    }
}