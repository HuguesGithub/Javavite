<?php
namespace src\Entity;

use src\Constant\ConstantConstant;
use src\Entity\Game;
use src\Entity\Player;

class Entity
{
    public function __construct(array $attributes=[])
    {
        if (!empty($attributes)) {
            foreach ($attributes as $key=>$value) {
                $this->{$key} = $value;
            }
        }
    }

    public function __toString(): string
    {
        return $this::class.ConstantConstant::CST_EOL;
    }

    public function initRepository($repositories=[])
    {
        while (!empty($repositories)) {
            $repository = array_shift($repositories);
            $this->{$repository} = new $repository;
        }
    }

    public function getField(string $field): mixed
    {
        return $this->{$field};
    }

    public function setField(string $field, $value): void
    {
        if ($value==null) {
            $value = ' ';
        }
        $this->{$field} = $value;
    }

    public function initTests(): void
    {
        $score = [];
        for ($i=1; $i<=20; $i++) {
            $score[$i] = 0;
        }
        $this->tests = [
            ConstantConstant::CST_GLOBAL => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_INFLICTED=>0,
                ConstantConstant::CST_FAIL=>0,
                ConstantConstant::CST_SCORE=>$score],
            ConstantConstant::CST_START => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_FAIL=>0,
                ConstantConstant::CST_SUCCESS=>0,
                ConstantConstant::CST_SCORE=>$score],
            ConstantConstant::CST_BODY => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_INFLICTED=>0,
                ConstantConstant::CST_FAIL=>0,
                ConstantConstant::CST_SCORE=>$score],
            ConstantConstant::CST_ENGINE => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_INFLICTED=>0,
                ConstantConstant::CST_FAIL=>0,
                ConstantConstant::CST_SCORE=>$score],
            ConstantConstant::CST_SUSPENSION => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_FAIL=>0,
                ConstantConstant::CST_SCORE=>$score],
            ConstantConstant::CST_PITSTOP => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_LONGPIT=>0,
                ConstantConstant::CST_FAIL=>0,
                ConstantConstant::CST_SUCCESS=>0,
                ConstantConstant::CST_SCORE=>$score]
        ];
    }

    public function initEvents(): void
    {
        $this->events = [
            ConstantConstant::CST_DNF => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_BODY=>0,
                ConstantConstant::CST_SUSPENSION=>0,
                ConstantConstant::CST_ENGINE=>0,
                ConstantConstant::CST_BLOCKED=>0,
                ConstantConstant::CST_TIRE=>0],
            ConstantConstant::CST_BRAKE => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_BRAKE=>0,
                ConstantConstant::CST_BLOCKED=>0,
                ConstantConstant::CST_FUEL=>0,
                ConstantConstant::CST_TRAIL=>0],
            ConstantConstant::CST_FUEL => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_1GEAR=>0,
                ConstantConstant::CST_2GEAR=>0,
                ConstantConstant::CST_3GEAR=>0],
            ConstantConstant::CST_TIRE => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_TIRE=>0,
                ConstantConstant::CST_QTY_BLOCKED=>0,
                ConstantConstant::CST_BLOCKED=>0],
            ConstantConstant::CST_TRAIL => [
                ConstantConstant::CST_QUANTITY=>0,
                ConstantConstant::CST_ACCEPTED=>0,
                ConstantConstant::CST_DECLINED=>0],
            ConstantConstant::CST_TAQ => [ConstantConstant::CST_QUANTITY=>0]
        ];
    }
}
