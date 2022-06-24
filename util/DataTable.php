<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataTable
 *
 * @author Jiskit
 */
class DataTable {

    //put your code here
    private $start = 0;
    private $end = 0;
    private $oDir = 'asc';
    private $oCulumn = '';
    private $search = '';
    private $draw = 1;

    public function getStart(): int {
        return $this->start;
    }

    public function getEnd(): int {
        return $this->end;
    }

    public function getOrderDir(): string {
        return $this->oDir;
    }

    public function getOrderColumn(): string {
        return $this->oCulumn;
    }

    public function getSearchValue(): string {
        return $this->search;
    }

    public function getDraw(): int {
        return $this->draw;
    }

    private function setStart(int $start) {
        $this->start = $start;
    }

    private function setEnd(int $end) {
        $this->end = $end;
    }

    private function setOrderDir(string $oDir) {
        $this->oDir = $oDir;
    }

    private function setOrderColumn(string $oColumn) {
        $this->oCulumn = $oColumn;
    }

    private function setSearchValue(string $search) {
        $this->search = $search;
    }

    private function setDraw(int $draw) {
        $this->draw = $draw;
    }

    private function init($data, array $columns) {
        (int) $draw = (int) filter_var($data["draw"], FILTER_SANITIZE_NUMBER_INT);
        (int) $end = (int) filter_var($data["length"], FILTER_SANITIZE_NUMBER_INT);
        (int) $start = (int) filter_var($data["start"], FILTER_SANITIZE_NUMBER_INT);
        (string) $oDir = (string) filter_var($data["order"][0]["dir"], FILTER_SANITIZE_STRING);
        (string) $oCol = (string) filter_var($data["order"][0]["column"], FILTER_SANITIZE_STRING);
        (string) $search = (string) filter_var($data["search"]["value"], FILTER_SANITIZE_STRING);
        $this->setDraw($draw);
        $this->setEnd($end > 0 ? $end : 1000000);
        $this->setOrderColumn($columns[$oCol]);
        $this->setOrderDir($oDir);
        $this->setSearchValue($search);
        $this->setStart($end > 0 ? $start : 0);
    }

    public function __construct($data, array $columns) {
        $this->init($data, $columns);
    }

}
