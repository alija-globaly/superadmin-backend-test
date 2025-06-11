<?php

namespace Agentcis\GlobalSearch;

use Nette\Tokenizer\Tokenizer;
use Nette\Tokenizer\Stream;

class SearchKeywordParser
{
    const T_MODULE = 1;
    const T_WHITESPACE = 2;
    const T_SEARCH_KEYWORD = 3;

    /** @var Tokenizer */
    private $tokenizer;

    /** @var Stream */
    private $stream;

    public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function parse($input)
    {
        try {
            $this->stream = $this->tokenizer->tokenize($input);
        } catch (\Exception $e) {
            new \InvalidArgumentException;
        }

        $result = [];
        while ($this->stream->nextToken()) {
            if ($this->stream->isCurrent(self::T_MODULE)) {
                $name = $this->stream->currentValue();
                $this->stream->nextUntil(self::T_SEARCH_KEYWORD);
                $content = $this->stream->joinUntil(self::T_MODULE);
                $result[] = ['module' => $name, 'keyword' => trim($content)];
            }
        }

        return $result;
    }
}

