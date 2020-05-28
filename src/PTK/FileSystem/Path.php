<?php
namespace PTK\FileSystem;

/**
 * Ferramentas para trabalhar com caminhos de arquivos e diretórios.
 * 
 * Trabalha tanto com arquivos/diretórios existentes, quanto com inexistentes.
 * 
 * Não faz nenhuma operação direta nos arquivos/diretórios. Para isso, 
 * utilizae PTK\FileSystem\FS, PTK\FileSystem\File e PTK\Filesystem\Directory
 *
 * @author Everton
 */
class Path
{

    protected string $path;

    public function __construct(string $path = '')
    {
        $this->path = $path;
    }

    public function get(): string
    {
        return $this->path;
    }

    public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function join(string ...$pieces): Path
    {
        $this->path = \join(DIRECTORY_SEPARATOR, $pieces);
        return $this;
    }

    protected function checkSlash(string $slash): void
    {
        if ($slash !== '/' && $slash !== '\\') {
            throw new InvalidArgumentException("Barra fornecida é inválida. Somente são aceitas \ ou /, porém [$slash] foi fornecido.");
        }
    }

    public function endSlash(string $slash = DIRECTORY_SEPARATOR): Path
    {
        $this->checkSlash($slash);
        $lastChar = substr($this->path, -1, 1);
        if ($lastChar !== '/' && $lastChar !== '\\') {
            $this->path .= $slash;
        }
        return $this;
    }

    public function slashes(string $slash = DIRECTORY_SEPARATOR): Path
    {
        $this->checkSlash($slash);
        $this->path = str_replace(['/', '\\'], $slash, $this->path);
        return $this;
    }
    
    public function __toString(): string
    {
        return $this->path;
    }
}
