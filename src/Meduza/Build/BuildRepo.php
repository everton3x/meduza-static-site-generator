<?php
namespace Meduza\Build;

/**
 * Repositório dos dados durante a contrução do conteúdo estático.
 *
 * @author Everton
 */
class BuildRepo
{

    protected array $data = [];

    public function __construct()
    {
        
    }

    public function set(string $path, $value): void
    {
        $pathStructured = explode('.', $path);

        $subject = & $this->data;

        $lastKey = array_pop($pathStructured);

        foreach ($pathStructured as $key) {
            try {
                $subject = &self::deepSearch($key, $subject);
            } catch (\OutOfBoundsException $ex) {
                //continue
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        $subject[$lastKey] = $value;
    }

    protected static function &deepSearch(string $key, array &$subject)
    {
        if (!key_exists($key, $subject)) {
            throw new \OutOfBoundsException("Valor [$key] não encontrado.");
        }

        return $subject[$key];
    }

    public function get(string $path)
    {
        $pathStructured = explode('.', $path);
        $subject = & $this->data;
        $lastKey = array_pop($pathStructured);

        foreach ($pathStructured as $key) {
            $subject = &self::deepSearch($key, $subject);
        }

        return $subject[$lastKey];
    }
}
