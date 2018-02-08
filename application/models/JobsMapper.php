<?php

class Model_JobsMapper
{
    public function getJobStatus($id)
    {
        Rad_Jobs::init();
        $status = Rad_Jobs::jobStatus($id);

        return $status;
    }
}