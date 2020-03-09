<?php

declare(strict_types=1);

namespace Yiisoft\Db\Sqlite\Condition;

use Yiisoft\Db\Query\Conditions\LikeConditionBuilder as BaseLikeConditionBuilder;

/**
 * {@inheritdoc}
 */
class LikeConditionBuilder extends BaseLikeConditionBuilder
{
    /**
     * {@inheritdoc}
     */
    protected ?string $escapeCharacter = '\\';
}
