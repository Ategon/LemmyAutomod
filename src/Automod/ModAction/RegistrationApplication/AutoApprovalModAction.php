<?php

namespace App\Automod\ModAction\RegistrationApplication;

use App\Automod\ModAction\AbstractModAction;
use App\Enum\FurtherAction;
use App\Repository\AutoApprovalRegexRepository;
use Rikudou\LemmyApi\Response\View\RegistrationApplicationView;

/**
 * @extends AbstractModAction<RegistrationApplicationView>
 */
final readonly class AutoApprovalModAction extends AbstractModAction
{
    public function __construct(
        private AutoApprovalRegexRepository $autoApprovalRegexRepository,
    ) {
    }

    public function shouldRun(object $object): bool
    {
        if (!$object instanceof RegistrationApplicationView) {
            return false;
        }
        $regexes = $this->autoApprovalRegexRepository->findAll();
        if (!count($regexes)) {
            return false;
        }
        $found = false;
        foreach ($regexes as $regexEntity) {
            $regex = str_replace('@', '\\@', $regexEntity->getRegex());
            $regex = "@{$regex}@i";
            if (!preg_match($regex, $object->registrationApplication->answer)) {
                continue;
            }
            $found = true;
            break;
        }

        return $found;
    }

    public function takeAction(object $object, array $previousActions = []): FurtherAction
    {
        $this->api->admin()->approveRegistrationApplication($object->registrationApplication);

        return FurtherAction::ShouldAbort;
    }

    public function getDescription(): ?string
    {
        return 'user has been automatically approved';
    }
}
