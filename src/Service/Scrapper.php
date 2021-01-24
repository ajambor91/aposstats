<?php

namespace App\Service;
class Scrapper
{
    const PAGE = 'https://www.licznikapostazji.pl/';
    const PATTERN = '/[<h3>]\d*.[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]*[(.|\ |a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ|,)][a-zA-Za-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]+[-]{0,1}[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]*[,|.][.]{0,1}[,]{0,1}[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ\/,.\- X]+[,]{1}\d{0,2}[.|,|\/]{0,1}\d{0,2}[.|,|\/]{0,1}\d{4}[<\/h3>]/';
    const DATA_ARRAY_LENGTH = 5;

    private $pageContent;
    private $scrappedData;

    public function getData(): array
    {
        $this->getPage();
        $this->removeHTMLTags();
        $this->replaceFirstDot();
        $this->removeInvalidChars();
        $this->hashData();
        $this->explodeArrItem();
        $this->validateScrappedData();
        $this->addDate();
        $this->removePersonalData();
        return $this->scrappedData;
    }

    private function getPage(): void
    {
        $this->pageContent = file_get_contents(self::PAGE);
    }

    private function removeHTMLTags(): void
    {
        $matches = [];
        preg_match_all(self::PATTERN, str_replace(['\n', '\r', ' '], '', $this->pageContent), $matches);
        $this->scrappedData = $matches;
    }

    private function replaceFirstDot(): void
    {
        $result = [];
        foreach ($this->scrappedData as $datum) {
            $result[] = preg_replace('/[.]/', ',', $datum, 1);
        }
        $this->scrappedData = $result[0];
    }

    private function explodeArrItem(): void
    {
        $result = [];
        foreach ($this->scrappedData as $datum) {
            $tmpArr = [];
            $tmpArr = explode(',', $datum['raw']);
            $tmpArr[] = $datum['hash'];
            $result[] = $tmpArr;
        }
        $this->scrappedData = $result;
    }

    private function removeInvalidChars(): void
    {
        $pattern = '/\d{0,2}[\/|.]/';
        $result = [];
        foreach ($this->scrappedData as $datum) {
            $result[] = preg_replace($pattern,'',str_replace(['>', '<',], '', $datum));
        }
        $this->scrappedData = $result;
    }

    private function hashData(): void
    {
        $result = [];
        foreach ($this->scrappedData as $datum) {
            $tmpArr = [
                'raw' => $datum,
                'hash' => md5($datum)
            ];
            $result[] = $tmpArr;
        }
        $this->scrappedData = $result;
    }

    private function addDate(): void
    {
        $result = [];
        foreach ($this->scrappedData as $datum) {
            $datum[] = new \DateTime();
            $result[] = $datum;
        }
        $this->scrappedData = $result;
    }

    private function removePersonalData(): void
    {
        $result = [];
        $i = 0;
        foreach ($this->scrappedData as $datum) {
            unset($datum[1]);
            $datum = array_values($datum);
            $tmpArray = [
                'ordinal_number' => $datum[0],
                'city' => $datum[1],
                'apostasy_year' => $datum[2],
                'hash' => $datum[3],
                'scrapped_at' => $datum[4]
            ];
            $result[] = $tmpArray;
        }
        $this->scrappedData = $result;
    }

    private function validateScrappedData(): void
    {
        $result = [];
        foreach ($this->scrappedData as $datum) {
            $tmpArray = $datum;
            if (count($datum) > self::DATA_ARRAY_LENGTH &&
                preg_match('/[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]/', $datum[2]) &&
                preg_match('/[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]/', $datum[3])) {
                $tmpArray = $datum;
                $tmpArray[2] = $datum[2] . $datum[3];
                unset($tmpArray[3]);
                $tmpArray = array_values($tmpArray);
            }
            $result[] = $tmpArray;
        }
        $this->scrappedData = $result;
    }

}