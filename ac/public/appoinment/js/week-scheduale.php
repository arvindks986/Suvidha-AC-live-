<script type="text/javascript">
var datesss = "<?php echo $datesss; ?>"; 
var myadataaaaaa = new Array();
<?php foreach($mychecduledata as $key => $val){ ?>
myadataaaaaa.push('<?php echo $val; ?>');
<?php } ?>

var HolidayDaata = new Array();
<?php foreach($finalHoliday as $hole => $leave){ ?>
HolidayDaata.push('<?php echo date("Y-m-d", strtotime($leave)); ?>');
<?php } ?>

 

var allBookedData = new Array();
<?php
$nom_end_date = '';
if(!empty($nomiantaion_end_date)){
	$nom_end_date = $nomiantaion_end_date;
}

foreach($AllBooked as $k => $v){ ?>
allBookedData.push({"key":<?php echo $k; ?>, "value": <?php echo $v; ?>});		
<?php } ?>
	
	var startdate = "<?php echo $nomiantaion_start_date; ?>";
	var enddate = "<?php echo $nom_end_date; ?>";

	function fetd(){
		var datedb='';
		var timedb='';
		var resd='';
		var dt='';
		var pppa=[];
		if(datesss.length>0){
		 var resd = datesss.split("***");
		  if(resd!=undefined){	
			 for(i=0; i<resd.length; i++){
				//if(resd[i]!=undefined){
				 dt = resd[i].split("+++");
				 var bbb = dt[0].replace("-", "");
				 var bbb = bbb.replace("-", "");
				 var ttt = dt[1].replace(":", "");
				 var fnl = bbb+ttt;
				 pppa.push(fnl);
				//} 
			 }
			 
			}
		}
	return pppa;
}

</script>

<script>
(function ($) {
  var DayScheduleSelector = function (el, options) {
    this.$el = $(el);
    this.options = $.extend({}, DayScheduleSelector.DEFAULTS, options);
    this.render();
    this.attachEvents();
    this.$selectingStart = null;
 }
  
 daysintext=[];
 daysinint=[];
 exactdat=[];

var d1 = new Date(startdate); 
var d2 = new Date(enddate); 


var Difference_In_Time = d2.getTime() - d1.getTime(); 
var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
//console.log(Difference_In_Days); 
for(i=1; i<Difference_In_Days+2; i++){ 
	var currentDate = new Date(new Date(startdate).getTime() + 24*i * 60 * 60 * 1000);
	//if(currentDate.toString().split(' ')[0]!='Sun') {
	var dateOffset = (24*60*60*1000) * 1; //5 days
	var myDate = new Date(currentDate);
	myDate.setTime(myDate.getTime() - dateOffset);
	//console.log(myDate);
	//aysintext.push(myDate);	
	daysintext.push(myDate.toString().split(' ')[0]);
	daysinint.push(i);
	exactdat.push(currentDate.toISOString().toString().split('T')[0]);		
	//}
}


  DayScheduleSelector.DEFAULTS = {

    //days        : [0, 1, 2, 3, 4, 5],  // Sun - Sat
    days        : daysinint,  // Sun - Sat
    startTime   : '11:00',                // HH:mm format
    endTime     : '16:00',                // HH:mm format
    interval    : 60,                     // minutes
    //stringDays  : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    stringDays  : daysintext,
    exactdat  : exactdat,
    template    : '<div class="day-schedule-selector">'         +
                    '<table class="schedule-table">'            +
                      '<thead class="schedule-header"></thead>' +
                      '<tbody class="schedule-rows"></tbody>'   +
                    '</table>'                                  +
                  '<div>'
  };

  /**
   * Render the calendar UI
   * @public
   */
  DayScheduleSelector.prototype.render = function () {
    this.$el.html(this.options.template);
    this.renderHeader();
    this.renderRows();
  };

  /**
   * Render the calendar header
   * @public
   */
  DayScheduleSelector.prototype.renderHeader = function () {
    var stringDays = this.options.stringDays
      , days = this.options.days
      , html = '';

    $.each(days, function (i, _) { 
	var dates = exactdat[i].split('-');
	var cdd = dates[2]+'-'+dates['1']+'-'+dates['0'];
	html += '<th style="text-align: center;font-size: 12px;width:auto;">' + (stringDays[i] +'<br>'+cdd  || '') + '</th>'; });
    this.$el.find('.schedule-header').html('<tr><th style="display:none;"></th>' + html+ + '</tr>');
  };

  /**
   * Render the calendar rows, including the time slots and labels
   * @public
   */
   
  var ssbbtt=0; 
  DayScheduleSelector.prototype.renderRows = function () {

	var today = new Date();
	var dd = String(today.getDate()).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
	var yyyy = today.getFullYear();
	today = yyyy + '-' + mm + '-' + dd;
	

    var start = this.options.startTime
      , end = this.options.endTime
      , interval = this.options.interval
      , days = this.options.days
      , $el = this.$el.find('.schedule-rows');
	
    $.each(generateDates(start, end, interval), function (i, d) {
      var daysInARow = $.map(new Array(days.length), function (_, i) {
		var datetimedata =hhmm(d)+'___'+days[i]+'***'+exactdat[i];
		var mdaat = datetimedata.replace(":", "");
		var mdaat = mdaat.replace("___", "");
		var mdaat = mdaat.replace("***", "");
		
		
		
		if(exactdat[i] > today ){
			//console.log(exactdat[i]); 
			//console.log("greater than eqoual"); 
		} else {
			//console.log(exactdat[i]);
			//console.log("lesst than"); 
		}
	 var ppp = fetd();
	 
	 var matcdd = exactdat[i].replace("-", "");
	 var matcdd = matcdd.replace("-", "");
	 var matttt = hhmm(d).replace(":", "");
	 var ftxt   = matcdd+matttt;
			//console.log(allBookedData);
			var isf='';
			let found = allBookedData.find(e => e.key==ftxt);
			if(found!==undefined){
			    
				var isf=found.value+'->'+ftxt;
			}
	
	
	  
	 if(myadataaaaaa.indexOf(ftxt) != -1){  
		 return '<td style="background: #009788;color:white;text-align:center;">'+hmmAmPm(d)+'</td>' 
	 }	 
	else if(exactdat[i] >= today ){	
			 
			 var bg=""
			 if(HolidayDaata.indexOf(exactdat[i]) != -1){  
			  bg="red";
			 } else {
			  bg="";
			 }
			
			
			var color="";
			var dddddddssss=''; 
			if(isf!=''){ 
			 var dddddddssss = isf.split('->');
			  if(dddddddssss[0]>5){
				  color='#FCB4B1';
			  } else {
				  color='#F9FBAE'; 
			  }
			}
			if(ftxt==dddddddssss[1]){
				return '<td class="time-slot '+mdaat+'" style="text-align:center;background:'+color+';" data-time="' + hhmm(d) + '" data-day="' + days[i] + '" id="'+ftxt+'" onclick=getval("'+datetimedata+'","'+ftxt+'");>'+hmmAmPm(d)+'</td>'	 
			} else {
				if(bg=='red'){				
				 return '<td style="background: #cccccc; opacity: .5;text-align:center;">'+hmmAmPm(d)+'</td>'	
					 				
				} else {
					return '<td class="time-slot '+mdaat+'" style="text-align:center;" data-time="' + hhmm(d) + '" data-day="' + days[i] + '" id="'+ftxt+'" onclick=getval("'+datetimedata+'","'+ftxt+'");>'+hmmAmPm(d)+'</td>'	
				}
			}
			
			

	  } else {
	 return '<td style="background: #cccccc; opacity: .5;text-align:center;">'+hmmAmPm(d)+'</td>'
	}      

    }).join();

      $el.append('<tr><td class="time-label" style="display:none;">' + hmmAmPm(d) + '</td>' + daysInARow + '</tr>');
    });
	
	ssbbtt++;
	
  };

  /**
   * Is the day schedule selector in selecting mode?
   * @public
   */
  DayScheduleSelector.prototype.isSelecting = function () {
    return !!this.$selectingStart;
  }

  DayScheduleSelector.prototype.select = function ($slot) { $slot.attr('data-selected', 'selected'); }
  DayScheduleSelector.prototype.deselect = function ($slot) { $slot.removeAttr('data-selected'); }

  function isSlotSelected($slot) { return $slot.is('[data-selected]'); }
  function isSlotSelecting($slot) { return $slot.is('[data-selecting]'); }

  /**
   * Get the selected time slots given a starting and a ending slot
   * @private
   * @returns {Array} An array of selected time slots
   */
  function getSelection(plugin, $a, $b) {
    var $slots, small, large, temp;
    if (!$a.hasClass('time-slot') || !$b.hasClass('time-slot') ||
        ($a.data('day') != $b.data('day'))) { return []; }
    $slots = plugin.$el.find('.time-slot[data-day="' + $a.data('day') + '"]');
    small = $slots.index($a); large = $slots.index($b);
    if (small > large) { temp = small; small = large; large = temp; }
    return $slots.slice(small, large + 1);
  }

  DayScheduleSelector.prototype.attachEvents = function () {
    var plugin = this
      , options = this.options
      , $slots;

    this.$el.on('click', '.time-slot', function () {
      var day = $(this).data('day');
      if (!plugin.isSelecting()) {  // if we are not in selecting mode
        if (isSlotSelected($(this))) { plugin.deselect($(this)); }
        else {  // then start selecting
          plugin.$selectingStart = $(this);
          $(this).attr('data-selecting', 'selecting');
         // plugin.$el.find('.time-slot').attr('data-disabled', 'disabled');
          //plugin.$el.find('.time-slot[data-day="' + day + '"]').removeAttr('data-disabled');
        }
      } else {  // if we are in selecting mode
        if (day == plugin.$selectingStart.data('day')) {  // if clicking on the same day column
          // then end of selection
          plugin.$el.find('.time-slot[data-day="' + day + '"]').filter('[data-selecting]')
            .attr('data-selected', 'selected').removeAttr('data-selecting');
          //plugin.$el.find('.time-slot').removeAttr('data-disabled');
          plugin.$el.trigger('selected.artsy.dayScheduleSelector', [getSelection(plugin, plugin.$selectingStart, $(this))]);
          plugin.$selectingStart = null;
        }
      }
    });

    this.$el.on('mouseover', '.time-slot', function () {
      var $slots, day, start, end, temp;
      if (plugin.isSelecting()) {  // if we are in selecting mode
        day = plugin.$selectingStart.data('day');
        $slots = plugin.$el.find('.time-slot[data-day="' + day + '"]');
        $slots.filter('[data-selecting]').removeAttr('data-selecting');
        start = $slots.index(plugin.$selectingStart);
        end = $slots.index(this);
        if (end < 0) return;  // not hovering on the same column
        if (start > end) { temp = start; start = end; end = temp; }
        $slots.slice(start, end + 1).attr('data-selecting', 'selecting');
      }
    });
  };

  /**
   * Serialize the selections
   * @public
   * @returns {Object} An object containing the selections of each day, e.g.
   *    {
   *      0: [],
   *      1: [["15:00", "16:30"]],
   *      2: [],
   *      3: [],
   *      5: [["09:00", "12:30"], ["15:00", "16:30"]],
   *      6: []
   *    }
   */
  DayScheduleSelector.prototype.serialize = function () {
    var plugin = this
      , selections = {};

    $.each(this.options.days, function (_, v) {
      var start, end;
      start = end = false; selections[v] = [];
      plugin.$el.find(".time-slot[data-day='" + v + "']").each(function () {
        // Start of selection
        if (isSlotSelected($(this)) && !start) {
          start = $(this).data('time');
        }

        // End of selection (I am not selected, so select until my previous one.)
        if (!isSlotSelected($(this)) && !!start) {
          end = $(this).data('time');
        }

        // End of selection (I am the last one :) .)
        if (isSlotSelected($(this)) && !!start && $(this).is(".time-slot[data-day='" + v + "']:last")) {
          end = secondsSinceMidnightToHhmm(
            hhmmToSecondsSinceMidnight($(this).data('time')) + plugin.options.interval * 60);
        }

        if (!!end) { selections[v].push([start, end]); start = end = false; }
      });
    })
    return selections;
  };

  /**
   * Deserialize the schedule and render on the UI
   * @public
   * @param {Object} schedule An object containing the schedule of each day, e.g.
   *    {
   *      0: [],
   *      1: [["15:00", "16:30"]],
   *      2: [],
   *      3: [],
   *      5: [["09:00", "12:30"], ["15:00", "16:30"]],
   *      6: []
   *    }
   */
  DayScheduleSelector.prototype.deserialize = function (schedule) {
    var plugin = this, i;
    $.each(schedule, function(d, ds) {
      var $slots = plugin.$el.find('.time-slot[data-day="' + d + '"]');
      $.each(ds, function(_, s) {
        for (i = 0; i < $slots.length; i++) {
          if ($slots.eq(i).data('time') >= s[1]) { break; }
          if ($slots.eq(i).data('time') >= s[0]) { plugin.select($slots.eq(i)); }
        }
      })
    });
  };

  // DayScheduleSelector Plugin Definition
  // =====================================

  function Plugin(option) {
    return this.each(function (){
      var $this   = $(this)
        , data    = $this.data('artsy.dayScheduleSelector')
        , options = typeof option == 'object' && option;

      if (!data) {
        $this.data('artsy.dayScheduleSelector', (data = new DayScheduleSelector(this, options)));
      }
    })
  }

  $.fn.dayScheduleSelector = Plugin;

  /**
   * Generate Date objects for each time slot in a day
   * @private
   * @param {String} start Start time in HH:mm format, e.g. "08:00"
   * @param {String} end End time in HH:mm format, e.g. "21:00"
   * @param {Number} interval Interval of each time slot in minutes, e.g. 30 (minutes)
   * @returns {Array} An array of Date objects representing the start time of the time slots
   */
  function generateDates(start, end, interval) {
    var numOfRows = Math.ceil(timeDiff(start, end) / interval);
    return $.map(new Array(numOfRows), function (_, i) {
      // need a dummy date to utilize the Date object
      return new Date(new Date(2000, 0, 1, start.split(':')[0], start.split(':')[1]).getTime() + i * interval * 60000);
    });
  }

  /**
   * Return time difference in minutes
   * @private
   */
  function timeDiff(start, end) {   // time in HH:mm format
    // need a dummy date to utilize the Date object
    return (new Date(2000, 0, 1, end.split(':')[0], end.split(':')[1]).getTime() -
            new Date(2000, 0, 1, start.split(':')[0], start.split(':')[1]).getTime()) / 60000;
  }

  /**
   * Convert a Date object to time in H:mm format with am/pm
   * @private
   * @returns {String} Time in H:mm format with am/pm, e.g. '9:30am'
   */
  function hmmAmPm(date) {
    var hours = date.getHours()
      , minutes = date.getMinutes()
      , ampm = hours >= 12 ? ' PM' : ' AM';
    return hours + ':' + ('0' + minutes).slice(-2) + ampm;
  }

  /**
   * Convert a Date object to time in HH:mm format
   * @private
   * @returns {String} Time in HH:mm format, e.g. '09:30'
   */
  function hhmm(date) {
    var hours = date.getHours()
      , minutes = date.getMinutes();
    return ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2);
  }

  function hhmmToSecondsSinceMidnight(hhmm) {
    var h = hhmm.split(':')[0]
      , m = hhmm.split(':')[1];
    return parseInt(h, 10) * 60 * 60 + parseInt(m, 10) * 60;
  }

  /**
   * Convert seconds since midnight to HH:mm string, and simply
   * ignore the seconds.
   */
  function secondsSinceMidnightToHhmm(seconds) {
    var minutes = Math.floor(seconds / 60);
    return ('0' + Math.floor(minutes / 60)).slice(-2) + ':' +
           ('0' + (minutes % 60)).slice(-2);
  }

  // Expose some utility functions
  window.DayScheduleSelector = {
    ssmToHhmm: secondsSinceMidnightToHhmm,
    hhmmToSsm: hhmmToSecondsSinceMidnight
  };

})(jQuery);


</script>