<?php
declare(strict_types=1);
namespace App\FavoriteMovie\Domain\VO;

//use App\Common\Domain\Aggregate\AggregateRoot;

class MoviesFavoritiesVo //extends AggregateRoot
{
    public  function  __construct(
        private readonly ?ImdbIdVo $imdbId,
        private readonly ?TitleVo $title,
        private readonly ?YearVo $year,
        private readonly ?PosterVo $poster
    ){}

    public static  function create(
                                        ?ImdbIdVo $imdbId,
                                        ?TitleVo $title,
                                        ?YearVo $year,
                                        ?PosterVo $poster
                                ):MoviesFavoritiesVo{
        try {
            return new self(
                              $imdbId,
                              $title,
                              $year,
                              $poster
                            );
        }catch(\Error $e){
            throw new \Error;
        }
    }
    
    public function imdbId():?ImdbIdVo
    { 
         return $this->imdbId;
    }    
    public function title():?TitleVo
    { 
         return $this->title;
    }
    public function year():?YearVo
    { 
         return $this->year;
    }
    public function poster():?PosterVo
    { 
        return $this->poster;
    }
    

    public function toArray():array
    {
        $toArray = [
            'imdbId' => $this->imdbId() ?  $this->imdbId()->value() : null,
            'title' => $this->title() ? $this->title()->value() : null,
            'year' => $this->year() ? $this->year()->value() : null,
            'poster' => $this->poster() ? $this->poster()->value() : null
        ];

        return $toArray;
    }

}