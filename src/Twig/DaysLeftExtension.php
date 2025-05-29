<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DaysLeftExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('days_left', [$this, 'calculateDaysLeft']),
        ];
    }
    public function calculateDaysLeft(\DateTimeInterface $dueAt): string
    {
        $now = new \DateTime();
        $diffInSeconds = $dueAt->getTimestamp() - $now->getTimestamp();
        $days = (int) floor($diffInSeconds / 86400);

        if ($days > 0) {
            return "$days days left";
        } elseif ($days === 0) {
            return "Due today";
        } else {
            return "Overdue by " . abs($days) . " days";
        }
    }
}
