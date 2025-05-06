<?php

namespace Modules\Installer\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @property string $config
 * @property int|string|null $value
 */
class Configuration extends Model
{
    protected $fillable = [
        'config',
        'value',
    ];

    /**
     * Check if the setup step matches the given step.
     *
     * @param int $step
     * @return bool
     */
    public static function setupStepCheck(int $step): bool
    {
        try {
            $data = Configuration::where('config', 'setup_stage')->first();

            if ($data && $step == $data['value']) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get the current setup step if it exists.
     *
     * @return int|false Returns the step number (int) if found, false otherwise
     */
    public static function stepExists(): int|false
    {
        try {
            $data = Configuration::where('config', 'setup_stage')->first();

            if ($data && isset($data['value'])) {
                $value = $data['value'];
                return is_numeric($value) ? (int)$value : false;
            }

            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * Update the setup step to the provided value.
     *
     * @param int $step
     * @return bool
     */
    public static function updateStep(int $step): bool
    {
        try {
            $configuration = Configuration::where('config', 'setup_stage')->firstOrFail();
            return $configuration->update(['value' => $step]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * Update the setup complete status.
     *
     * @param int $step
     * @return bool
     */
    public static function updateCompeteStatus(int $step): bool
    {
        try {
            $configuration = Configuration::where('config', 'setup_complete')->firstOrFail();
            return $configuration->update(['value' => $step]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
