
module.exports = {
	schedule
};

function schedule ( task, interval ) {

	interval = interval * 1000;
	var status = null;
	var timerId = null;

	/*
	 *
	 * This function simply schedules an execution of the task based on the
	 * given interval
	 *
	 */
	function scheduleNextExecution () {
		if ( status == "running" ) {
			timerId = setTimeout( runAndScheduleTask, interval );
		}
	}

	/*
	 *
	 * This function simply runs the task,
	 * passing in the `scheduleNextExecution` function as a callback to be
	 * exectued once the task is finished. This is because the tasks are async.
	 *
	 */
	function runAndScheduleTask () {
		task( scheduleNextExecution );
	}

	/*
	 *
	 * This is the API that we're returning
	 *
	 */
	return {
		start: function () {
			if ( status == "running" ) return;
			status = "running";
			scheduleNextExecution();
		},
		stop: function () {
			clearTimeout( timerId );
			timerId = null;
			status = "stopped";
		},
	};

}
