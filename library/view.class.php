<?php

declare(strict_types=1);

/**
 * View Class
 * Responsible for binding variables and rendering template/view files.
 */
#[\AllowDynamicProperties]
class View
{
    public string $viewName;
    public array $data = [];
    public bool $isRendered = false;

    public function __construct(string $viewName, array $data = [])
    {
        $this->viewName = $viewName;
        $this->data = $data;
    }

    /**
     * Bind a variable to the view
     */
    public function bind(string $name, mixed $value = ''): self
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Render the view file
     */
    public function render(): void
    {
        if ($this->isRendered) {
            return;
        }

        $this->isRendered = true;
        extract($this->data);

        $viewPath = APP_PATH . DS . 'views' . DS . str_replace(['/', '\\'], DS, $this->viewName) . '.view.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            http_response_code(404);
            $notFoundView = APP_PATH . DS . 'views' . DS . '404.view.php';
            if (file_exists($notFoundView) && $this->viewName !== '404') {
                $errorMsg = "View not found: " . htmlspecialchars($this->viewName, ENT_QUOTES, 'UTF-8');
                require $notFoundView;
            } else {
                echo "<h3>View not found: " . htmlspecialchars($this->viewName, ENT_QUOTES, 'UTF-8') . "</h3>";
            }
        }
    }

    /**
     * Automatically render the view when object goes out of scope if not rendered yet
     */
    public function __destruct()
    {
        if (!$this->isRendered) {
            $this->render();
        }
    }
}
