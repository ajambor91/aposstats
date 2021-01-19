<?php

namespace App\Service;
class Scrapper
{
    const PAGE = 'https://www.licznikapostazji.pl/';
    const PATTERN = '/[<h3>]\d*.[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]*[(.|\ |a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ|,)][a-zA-Za-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]+[-]{0,1}[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ]*[,|.][.]{0,1}[,]{0,1}[a-zA-ZźżńśćąęłóŹŻŃŚĆĄĘŁÓ\/,.\- X]+[,]{1}\d{0,2}[.]{0,1}\d{0,2}[.]{0,1}\d+[<\/h3>]/';

    public function fillDatabase(): array
    {
        $pageContent = $this->getPage();
        $array = $this->removeHTMLTags($pageContent);
        $array = $this->replaceFirstDot($array);
        $array = $this->removeInvalidChars($array);
        $array = $this->hashData($array);
        $array = $this->explodeArrItem($array);
        $array = $this->addDate($array);
        $array = $this->removePersonalData($array);
        return $array;
    }

    private function getPage(): string
    {
        return file_get_contents(self::PAGE);
    }

    private function removeHTMLTags(string $page): array
    {
        $matches = [];
        preg_match_all(self::PATTERN, str_replace(['\n', '\r', ' '], '', $page), $matches);
        return $matches;
    }

    private function replaceFirstDot(array $data): array
    {
        $result = [];
        foreach ($data as $datum) {
            $result[] = preg_replace('/[.]/', ',', $datum, 1);
        }
        return $result[0];
    }

    private function explodeArrItem(array $data): array
    {
        $result = [];
        foreach ($data as $datum) {
            $tmpArr = [];
            $tmpArr = explode(',', $datum['raw']);
            $tmpArr[] = $datum['hash'];
            $result[] = $tmpArr;
        }
        return $result;
    }

    private function removeInvalidChars(array $data): array
    {
        $result = [];
        foreach ($data as $datum) {
            $result[] = str_replace(['>', '<',], '', $datum);
        }

        return $result;
    }

    private function hashData(array $data): array
    {
        $result = [];
        foreach ($data as $datum) {
            $tmpArr = [
                'raw' => $datum,
                'hash' => md5($datum)
            ];
            $result[] = $tmpArr;
        }
        return $result;
    }

    private function addDate(array $data): array
    {
        $result = [];
        foreach ($data as $datum) {
            $datum[] = new \DateTime();
            $result[] = $datum;
        }
        return $result;
    }

    private function removePersonalData(array $data): array
    {
        $result = [];
        foreach ($data as $datum) {
            unset($datum[1]);
            $result[] = array_values($datum);
        }
        return $result;
    }

}