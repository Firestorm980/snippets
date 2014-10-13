<?php

/**
 * Creates the calendar table on the front end via the template and AJAX.
 * This function uses the get_all_calendars() function to get all calendar events from Google before making the table.
 * The event modal template is in a seperate file for consistency with other templates. (/inc/modal-event.php)
 * 
 * @param  [interger] $month 	The number of the month without leading zeros.
 * @param  [interger] $year  	The year to be queried.
 * @return [html]        		The output HTML table that is displayed on the front end.
 */
function build_calendar($month, $year){

	// AJAX parameters
	$is_ajax = ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) ? true : false;
	$params = array('month' => $month, 'year' => $year);
	$options = ($is_ajax) ? $_REQUEST['params'] : $params;

	$firstDayOfMonth = mktime( 0, 0, 0, $options['month'], 1, $options['year'] );

	// Get our events (only grab the requested month and year)
	$eventOptions = array(
		'orderBy' => 'startTime',
		'singleEvents' => 'true',
		'timeMax' => date('c', strtotime("first day of +1 month", $firstDayOfMonth )),
		'timeMin' => date('c', strtotime("last day of -1 month", $firstDayOfMonth ))
	);
	$events = get_all_calendars( $eventOptions );

	// Setup
	$daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	$daysOfWeekAbv = array('Sun', 'Mon', 'Tues', 'Wed', 'Thu', 'Fri', 'Sat');
	$numberDays = date('t', $firstDayOfMonth);
	$dateComponents = getdate($firstDayOfMonth);
	$dayOfWeek = $dateComponents['wday'];

	ob_start();
	?>
		<table id="calendarTable" class="calendar-table" cellpadding="0" cellspacing="0" border="0">
			<thead>
			<?php 
				// The days of the week
				$dayCount = 0;
				foreach ($daysOfWeek as $day): ?>
				<th>
					<span class="large-screen"><?php echo $day; ?></span>
					<span class="small-screen"><?php echo $daysOfWeekAbv[$dayCount]; ?></span>
				</th>
			<?php 
					$dayCount++;
				endforeach; 
			?>
			</thead>
			<tbody>
				<tr>
				<?php 
					// Current day of the month
					$currentDay = 1; 

					// If our starting day is not the first day of the week, print out enough blank days before it.
					if ( $dayOfWeek != 0 ):
						$proceedingDays = $dayOfWeek;

						while ( $proceedingDays > 0 ):
				?>
							<td class="empty">&nbsp;</td>
				<?php 
							$proceedingDays--; 
						endwhile;
					endif; 
				?>

				<?php 
					// Keep looping though days
					while ( $currentDay <= $numberDays ):

						// If this is the last day of the week, reset the counter and make a new row
						if ($dayOfWeek == 7):
							$dayOfWeek = 0; 
				?>
							</tr><tr>
					<?php 
						endif; 

						// Some variables for comparison and proper rel attribute
						$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
						$dateCode = $options['year'].'-'.$options['month'].'-'.$currentDayRel;

						// Check if this date was in the past
						$is_past = ( strtotime( $dateCode ) < strtotime(date('Y-m-d')) ) ? true : false;

						// Check if this date is today
						$is_today = ( strtotime( $dateCode ) == strtotime(date('Y-m-d')) ) ? true : false;

						// Does this day have any events?
						$hasEvents = false;
						foreach ($events as $event) {
							// Event date information
							$startTime = ( isset($event['start']['dateTime']) ) ? date_create($event['start']['dateTime']) : null;
							$startDate = ( isset($event['start']['date']) ) ? date_create($event['start']['date']) : null;
							$endDate = ( isset($event['end']['date']) ) ? date_create($event['end']['date'])->modify('-1 day') : null;

							// Is the event single day, or multi day?
							$is_singleDay = ( !is_null($startTime) ) ; 
							$is_multiDay = ( !is_null($startDate) ) ; 

							// If the processed event falls on this day, create a list item for it
							if (
								// Single day events
								$is_singleDay && strtotime( $dateCode ) == strtotime( date_format( $startTime, 'Y-m-d') ) ||

								// Multi day events
								$is_multiDay && 
								strtotime( $dateCode ) >= strtotime( date_format( $startDate, 'Y-m-d') ) &&
        						strtotime( $dateCode ) <= strtotime( date_format( $endDate, 'Y-m-d') )
								)
							{
        						$hasEvents = true;
        						break;
        					}
						}

						// Date classes
						$classes = array();
						$classes[] = ( $is_today ) ? 'today' : '';
						$classes[] = ( $is_past ) ? 'past' : '';
						$classes[] = ( $hasEvents ) ? 'has-events' : '';
					?>

					<td rel="<?php echo $dateCode; ?>" data-date-string="<?php echo date('l, F jS', strtotime( $dateCode ) ); ?>" class="<?php echo implode(' ', $classes); ?>">
						<div class="day-container">
							<span class="day large-screen"><?php echo $currentDay; ?></span>
							
							<?php if ( $hasEvents ): ?>
								<button type="button" class="day small-screen button button-day button-modal" data-target="#dayModal">
									<span><?php echo $currentDay; ?></span>
								</button>
							<?php else: ?>
								<span class="day small-screen"><?php echo $currentDay; ?></span>
							<?php endif; ?>

							<?php if ( $hasEvents ): ?>
							<ul class="day-events filter-items-list">
							<?php 
								// Our events loop
								foreach ($events as $event): 

									// Event date information

									// Single Day
									$startTime = ( isset($event['start']['dateTime']) ) ? date_create($event['start']['dateTime']) : null;
									$endTime = ( isset($event['end']['dateTime']) ) ? date_create($event['end']['dateTime']) : null;

									// Multi Day
									// @note: The endDate is modified back 1 day because Google will report the incorrect day as being the end.
									$startDate = ( isset($event['start']['date']) ) ? date_create($event['start']['date']) : null;
									$endDate = ( isset($event['end']['date']) ) ? date_create($event['end']['date'])->modify('-1 day') : null;

									// Is the event single day, or multi day?
									$is_singleDay = ( !is_null($startTime) ) ; 
									$is_multiDay = ( !is_null($startDate) ) ; 

									// If the processed event falls on this day, create a list item for it
									if (
										// Single day events
										$is_singleDay && strtotime( $dateCode ) == strtotime( date_format( $startTime, 'Y-m-d') ) ||

										// Multi day events
										$is_multiDay && 
										strtotime( $dateCode ) >= strtotime( date_format( $startDate, 'Y-m-d') ) &&
		        						strtotime( $dateCode ) <= strtotime( date_format( $endDate, 'Y-m-d') )
									):

										// Event meta data
										$event_ID = $event['id'];
										$event_summary = $event['summary'];
										$event_description = $event['description'];

										$firstDay = ( !is_null($startDate) && strtotime( $dateCode ) == strtotime( date_format( $startDate, 'Y-m-d') ) ) ? 'day-first' : '';
										$lastDay = ( !is_null($endDate) && strtotime( $dateCode ) == strtotime( date_format( $endDate, 'Y-m-d') ) ) ? 'day-last' : ''; 

										// Event specific classes
										$eventClasses = array();
										$eventClasses[] = $firstDay;
										$eventClasses[] = $lastDay;
										$eventClasses[] = ( $is_singleDay ) ? 'day-single' : 'day-multi';
										$eventClasses[] = strtolower( str_replace(' ', '-', $event['organizer']['displayName'] ) );

                					?>
                					<li class="day-event item <?php echo implode(' ', $eventClasses); ?>" data-filters="<?php echo strtolower( str_replace(' ', '_', $event['organizer']['displayName'] ) ); ?>">

                						<?php
                							// Event isn't in the past? Output the full modal code. Otherwise just the name.
                							if ( !$is_past ): 
                						?>
	                						<button class="button button-link button-modal" type="button" data-target="#modal-day-<?php echo $currentDay; ?>-<?php echo $event_ID; ?>">
	                							<span><?php echo $event_summary; ?></span>
	                						</button>
	                						
	                						<?php
	                							// Our modal window template 
	                							include(locate_template('/inc/modal-event.php')); 
	                						?>

										<?php else: ?>
											<span><?php echo $event_summary; ?></span>
										<?php endif; ?>
                					</li>
                				<?php endif; ?>
							<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
					</td>
				<?php
						// Increment our day of month and day of week counters.
						$currentDay++;
						$dayOfWeek++;

					endwhile; 

					// At the end of the month, fill in the blank days.
					if ( $dayOfWeek != 7 ):
						$remainingDays = 7 - $dayOfWeek;
						
						while ( $remainingDays > 0 ): 
				?>
						<td class="empty">&nbsp;</td>
				<?php 
							$remainingDays--;
						endwhile;
					endif; 
				?>
				</tr>
			</tbody>
		</table>
	<?

	$html = ob_get_clean();

	// Give back appropriate results
	if (!$is_ajax){
		return $html;
	}
	else {
		echo $html;
	}
	die();
}

/*
Optional WordPress stuff.
add_action( 'wp_ajax_nopriv_get_calendar', 'build_calendar' );
add_action( 'wp_ajax_get_calendar', 'build_calendar' );
*/


/**
 * Function builds out a large portion of the calendar header, which heavily uses PHP date functions that would otherwise be difficult in Javascript alone.
 * Returns the code to the front end via the template and AJAX.
 * 
 * @param  [interger] $month 	The number of the month without leading zeros.
 * @param  [interger] $year  	The year to be queried.
 * @return [html]        		The output HTML table that is displayed on the front end.
 */
function build_calendar_header($month, $year){
	// AJAX parameters
	$is_ajax = ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) ? true : false;
	$params = array('month' => $month, 'year' => $year);
	$options = ($is_ajax) ? $_REQUEST['params'] : $params;

	// Create a timestamp of the query date
	$timestamp = mktime( 0, 0, 0, $options['month'], 1, $options['year'] );

	ob_start();
	?>
		<div class="calendar-header-dynamic" id="calendarHeaderDynamic">
			<div id="months" class="months">
				<button class="button button-month month calendar-control" id="previousMonth" type="button" data-direction="previous" data-month="<?php echo date('n', strtotime("-1 month", $timestamp ));?>" data-year="<?php echo date('Y', strtotime("-1 month", $timestamp ));?>">
					<span class="large-screen"><?php echo date("F Y", strtotime("-1 month", $timestamp ));?></span>
					<span class="small-screen"><?php echo date("M Y", strtotime("-1 month", $timestamp ));?></span>
				</button>
				<h1 class="month active" id="activeMonth">
					<span><?php echo date('F Y', $timestamp ); ?></span>
				</h1>
				<button class="button button-month month calendar-control" id="nextMonth" type="button" data-direction="next" data-month="<?php echo date('n', strtotime("+1 month", $timestamp ));?>" data-year="<?php echo date('Y', strtotime("+1 month", $timestamp ));?>">
					<span class="large-screen"><?php echo date("F Y", strtotime("+1 month", $timestamp ));?></span>
					<span class="small-screen"><?php echo date("M Y", strtotime("+1 month", $timestamp ));?></span>
				</button>
			</div>
			<!-- #months -->

			<div id="controls" class="controls">
				<button class="button button-control previous calendar-control" type="button" data-direction="previous" data-month="<?php echo date('n', strtotime("-1 month", $timestamp ));?>" data-year="<?php echo date('Y', strtotime("-1 month", $timestamp ));?>" >
					<span class="screen-reader-text">Previous Month</span>
					<i class="icon arrow_left_alt" aria-hidden="true"></i>
				</button>
				<button class="button button-control next calendar-control" type="button" data-direction="next" data-month="<?php echo date('n', strtotime("+1 month", $timestamp ));?>" data-year="<?php echo date('Y', strtotime("+1 month", $timestamp ));?>">
					<span class="screen-reader-text">Next Month</span>
					<i class="icon arrow_right_alt" aria-hidden="true"></i>
				</button>
			</div>
			<!-- #controls -->
		</div>
	<?

	$html = ob_get_clean();

	// Give back appropriate results
	if (!$is_ajax){
		return $html;
	}
	else {
		echo $html;
	}
	die();

}
/*
Optional WordPress stuff.
add_action( 'wp_ajax_nopriv_get_calendar_header', 'build_calendar_header' );
add_action( 'wp_ajax_get_calendar_header', 'build_calendar_header' );
*/
?>