<?php
namespace PTK\FileSystem;

/**
 * Ferramentas para manipulação de diretórios.
 *
 * @author Everton
 */
class Directory
{

    const ALL = 0;
    const ONLY_DIR = 1;
    const ONLY_FILES = 2;

    protected string $path;

    public function __construct(string $path = '')
    {
        if (!is_dir($path)) {
            throw new \Exception("Caminho [$path] não é um diretório ou não existe.");
        }
        $this->path = $path;
    }

    public static function create(string $path): Directory
    {
        if (!mkdir($path, 0777, true)) {
            throw new \Exception("Não foi possível criar o diretório [$path].");
        }

        return new Directory($path);
    }

    public function iterator(bool $recursive = true): \Iterator
    {
        if ($recursive === true) {
            return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path));
        } else {
            return new \DirectoryIterator($this->path);
        }
    }

    protected function filterByMode(int $mode, $node)
    {
        switch ($mode) {
            case self::ALL:
                return $node;
                break;
            case self::ONLY_DIR:
                if ($node->isDir()) {
                    return $node;
                }
                break;
            case self::ONLY_FILES:
                if ($node->isFile()) {
                    return $node;
                }
                break;
        }
        return false;
    }

    public function read(bool $recursive = true, int $mode = self::ALL): array
    {
        $iterator = $this->iterator($recursive);
        $nodes = [];

        while ($iterator->valid()) {
            if ($iterator->isDot()) {
                $iterator->next();
                continue;
            }
            if (($node = $this->filterByMode($mode, $iterator->current()))) {
                $nodes[$node->getPathname()] = $node;
            }
            $iterator->next();
        }

        return $nodes;
    }

    public function realpath(): Directory
    {
        $this->path = (new Path(\realpath($this->path)))->endSlash()->get();
        return $this;
    }

    public function get(bool $object = false)
    {
        if ($object) {
            return new \Directory($this->path);
        } else {
            return $this->path;
        }
    }

    public function __toString(): string
    {
        return $this->path;
    }
    
    public function remove(): Directory
    {
//        print_r($this->path);
        $this->path = (new Path($this->path))->slashes(DIRECTORY_SEPARATOR)->endSlash(DIRECTORY_SEPARATOR);
//        echo $this->path, PHP_EOL;exit();
        $files = array_diff(scandir($this->path), array('.', '..'));
//        print_r($files);exit();
        foreach ($files as $file) {
            if(is_dir("{$this->path}$file")){
                (new Directory("{$this->path}$file"))->remove();
//                echo "Entra em {$this->path}$file", PHP_EOL;
            }else{
//                echo "exclui {$this->path}$file", PHP_EOL;
                unlink("{$this->path}$file");
            }
        }
        rmdir($this->path);
        
        return $this;
    }
}
