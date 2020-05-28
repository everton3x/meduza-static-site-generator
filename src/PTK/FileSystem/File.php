<?php
namespace PTK\FileSystem;

/**
 * Ferramentas para manipulação de arquivos
 *
 * @author Everton
 */
class File
{

    const MODE_R = 'r';
    const MODE_R_PLUS = 'r+';
    const MODE_W = 'w';
    const MODE_W_PLUS = 'w+';
    const MODE_A = 'a';
    const MODE_A_PLUS = 'a+';
    const MODE_X = 'x';
    const MODE_X_PLUS = 'x+';

    protected string $path;
    protected string $mode;
    protected $handle = null;

    public function __construct(string $path = '', string $mode = self::MODE_R_PLUS)
    {
        $this->path = $path;
        $this->mode = $mode;

        if ($path) {
            $this->open($this->path, $this->mode);
        }
    }

    public function open(string $path = '', string $mode = self::MODE_R_PLUS): File
    {
        if ($path) {
            $this->path = $path;
        }
        if ($mode) {
            $this->mode = $mode;
        }

        if (($this->handle = fopen($this->path, $this->mode)) === false) {
            throw new \Exception("Falha ao abrir [{$this->path}] no modo [{$this->mode}]");
        }

        return $this;
    }
    
    public function getLine(bool $trim = true): string
    {
        if(($line = fgets($this->handle)) === false){
            $line = '';
        }
        if($trim){
            $line = trim($line);
        }
        return $line;
    }
    
    public function __destruct()
    {
        if($this->handle){
            @fclose($this->handle);
        }
    }
    
    public function close(): File
    {
        @fclose($this->handle);
        return $this;
    }
    
    public function __toString(): string
    {
        return $this->path;
    }
}
