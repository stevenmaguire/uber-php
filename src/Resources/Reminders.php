<?php namespace Stevenmaguire\Uber\Resources;

trait Reminders
{
    /**
     * Cancels a specific reminder.
     *
     * The Reminders endpoint allows you to remove any reminder in the pending
     * state from being sent.
     *
     * @param    string   $reminderId   Reminder id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-delete
     */
    public function cancelReminder($reminderId)
    {
        return $this->request('delete', 'reminders/'.$reminderId);
    }

    /**
     * Creates a new reminder.
     *
     * The Reminders endpoint allows developers to set a reminder for a future
     * trip.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/reminders-post
     */
    public function createReminder($attributes)
    {
        return $this->request('post', 'reminders', $attributes);
    }

    /**
     * Fetches a specific reminder.
     *
     * The Reminders endpoint allows you to get the status of an existing ride
     * reminder.
     *
     * @param    string   $reminderId   Reminder id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-get
     */
    public function getReminder($reminderId)
    {
        return $this->request('get', 'reminders/'.$reminderId);
    }

    /**
     * Updates a specific reminder.
     *
     * The Reminders endpoint allows you to update an existing reminder.
     *
     * @param string      $reminderId   Reminder id
     * @param array       $attributes   Query attributes
     *
     * @return  stdClass                The JSON response from the request
     *
     * @see     https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-patch
     */
    public function setReminder($reminderId, $attributes = [])
    {
        return $this->request('put', 'reminders/'.$reminderId, $attributes);
    }
}
