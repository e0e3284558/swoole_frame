<?php

class Index
{
    public function get()
    {
        echo "index->get()";
    }

    public function demo()
    {
        echo Index::get();
    }
}

(new Index())->demo();