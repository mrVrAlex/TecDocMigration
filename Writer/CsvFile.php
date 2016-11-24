<?php

namespace OpsWay\TecDocMigration\Writer;

class CsvFile implements WriterInterface
{
    protected $file;

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        if (!$this->file) {
            $this->file = fopen('TEST-'.time().'.csv', 'w+');
            fputcsv($this->file, array_keys($item));
        }
        return fputcsv($this->file, $item);
    }

    public function __destruct()
    {
        if ($this->file) {
            fclose($this->file);
        }
    }
}
