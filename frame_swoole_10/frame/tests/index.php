<?php
/**
 *
 */
class Index
{
    public function get()
    {
        echo "index -> get()";
    }

    public function demo()
    { 
        echo static::get();
    }
}
(new Index)->demo();
  // echo Index::get();
