<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Checker\Detector;

/**
 * A check that takes per-assignment configuration entered in the admin (e.g. a CSS/XPath selector
 * and an assertion). Implementing this lets operators define ad-hoc tests with no code.
 */
interface ConfigurableIssueDetectorInterface extends IssueDetectorInterface
{
    /**
     * The form type used to edit this check's configuration in the admin.
     *
     * @return class-string
     */
    public function getConfigFormType(): string;
}
