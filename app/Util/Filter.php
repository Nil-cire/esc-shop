<?php

class Filter
{
    function not_null_filter($value) {
        return $value !== NULL;
    }
}