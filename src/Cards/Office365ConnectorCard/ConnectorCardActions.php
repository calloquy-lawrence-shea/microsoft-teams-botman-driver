<?php

namespace MicrosoftTeamsDriver\Cards\Office365ConnectorCard;

class ConnectorCardActions
{
    public const VIEW_ACTION = 'ViewAction';

    protected array $actions;

    public function addAction(string $typeAction, string $title, array $target = []): self
    {
        $this->actions[] = collect([
            '@context' => 'http://schema.org',
            '@type' => $typeAction,
            'name' => $title,
            'target' => $target
        ]);

        return $this;
    }

    /**
     * @param string $title
     * @param string|array $value
     * @return $this
     */
    public function addInvokeAction(string $title, $value): self
    {
        $this->actions[] = collect([
            '@context' => 'http://schema.org',
            '@type' => 'ReadAction',
            'name' => $title,
            'target' => [
                $value
            ]
        ]);

        return $this;
    }

    public function getPayload(): array
    {
        return $this->actions;
    }
}
