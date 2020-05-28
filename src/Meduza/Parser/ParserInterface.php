<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Meduza\Parser;

/**
 * Interface to content parsers.
 * 
 * @author Everton
 */
interface ParserInterface
{
    public function __construct();
    
    public function parse(string $content): string;
}
