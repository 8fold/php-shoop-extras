<?php
declare(strict_types=1);

namespace Eightfold\ShoopShelf\Filter;

use Eightfold\Foldable\Filter;

use Eightfold\Foldable\Foldable;

use Eightfold\ShoopShelf\Shoop;
use Eightfold\ShoopShelf\Apply;

class UriQuery extends Filter
{
    private $queryDivider = "?";
    private $paramDivider = "&";
    private $includeDivider = false;
    private $asString = true;

    public function __construct($includeDivider = false, $asString = true)
    {
        if (is_a($includeDivider, Foldable::class)) {
            $includeDivider = $includeDivider->unfold();
        }
        $this->includeDivider = $includeDivider;

        if (is_a($asString, Foldable::class)) {
            $asString = $asString->unfold();
        }
        $this->asString = $asString;
    }

    public function __invoke($using)// : string|array
    {
        if (is_a($using, Foldable::class)) {
            $using = $using->unfold();
        }

        $array = Shoop::this($using)->divide($this->queryDivider, false, 2);

        if ($array->length()->is(2)->efToBoolean()) {
            $fragment = Apply::uriFragment(true)->unfoldUsing($using);
            $string   = $array->last()->unfold();
            $query    = str_replace($fragment, "", $string);
            die(var_dump($query));
            $queryString = Shoop::this($array[1])->minus([

                ], false, false);

            if ($this->asString and $this->includeDivider) {
                return Apply::prepend($this->queryDivider)
                    ->unfoldUsing($queryString->unfold());

            } elseif ($this->asString and ! $this->includeDivider) {
                return $queryString->unfold();

            } elseif (! $this->asString) {
                $dictionary = [];
                $inter = $queryString->asArray($this->paramDivider, false);
                foreach ($inter as $queryParam) {
                    list($param, $value) = Shoop::this($queryParam)->asArray("=", false, 2)->unfold();
                    $dictionary[$param] = $value;

                }
                return $dictionary;

            }
        }
        return "";
    }
}
