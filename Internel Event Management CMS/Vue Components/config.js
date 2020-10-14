const apiDomain = Laravel.apiDomain;
export const siteName = Laravel.siteName;
export const siteUrl = Laravel.siteUrl;

export const api = {
	login: apiDomain + '/authenticate',
	currentUser: apiDomain + '/user',
	updateUserProfile: apiDomain + '/user/profile/update',
	updateUserPassword: apiDomain + '/user/password/update',

	//	registration
	register: apiDomain + '/users/store',
	registerEventInternalUser: apiDomain + '/event_registrants/store',
	showRegisterInternalUser: apiDomain + '/event_registrants/show',
	editRegisterInternalUser: apiDomain + '/event_registrants/edit',
	deleteRegisterEventInternalUser: apiDomain + '/event_registrants/delete/',

	registerCourseInternalUser: apiDomain + '/course_registrants/store',
	deleteRegisterCourseInternalUser: apiDomain + '/course_registrants/delete/',


	//events
	event: apiDomain + '/event/details/',
	editEvent: apiDomain + '/event/show/',
	addNewEvent: apiDomain + '/event/store',
	deleteEvent: apiDomain + '/event/delete/',
	updateEvent: apiDomain + '/event/edit',

	//event hotels
	getEventHotels: apiDomain + '/hotel/show',
	addHotel: apiDomain + '/hotel/store',
	updateHotel: apiDomain + '/hotel/edit',
	deleteHotel: apiDomain + '/hotel/delete/',


	//event foods
	addFood: apiDomain + '/food/store',
	getEventFood: apiDomain + '/food/show',
	updateFood: apiDomain + '/food/edit',
	deleteFood: apiDomain + '/food/delete/',


	// give away
	addGiveAway: apiDomain + '/give_away/store',
	addGiveAwaySizes: apiDomain + '/give_away_sizes/store',
	editGiveAwaySizes: apiDomain + '/give_away_sizes/edit',
	getGiveAway: apiDomain + '/give_away/show',
	updateGiveAway: apiDomain + '/give_away/edit',
	deleteGiveAway: apiDomain + '/give_away/delete/',


	//register event course
	registerEventCourse: apiDomain + '/event/register/course',

	//course details
	addCoursesDetails: apiDomain + '/course/details/store',
	updateCoursesDetails: apiDomain + '/course/details/edit',
	//search course
	coursesSearch: apiDomain + '/course/search',
	//view course details
	viewCourse: apiDomain + '/course/details/show/',
	editCourse: apiDomain + '/course/show/',
	deletCourse: apiDomain + '/course/details/delete/',

	//time interval.
	startTimeInterval: apiDomain + '/start_time_interval',
	endTimeInterval: apiDomain + '/end_time_interval',

	//show event courses
	courses: apiDomain + '/course/show',

	//users
	internalUsers: apiDomain + '/users/show/internal',
	getEventsexternaleUsers: apiDomain + '/users/show/event/external',
	getCoursesexternaleUsers: apiDomain + '/users/show/course/external',
	externalUsers: apiDomain + '/users/show/external',
	updateUser: apiDomain + '/users/edit',
	userType: apiDomain + '/users/show/type',
	employeeInternalSearch: apiDomain + '/users/search/internal',
	employeeExternalSearch: apiDomain + '/users/search/external/user',
	employeeExternalUserSearch: apiDomain + '/users/search/external_search',
	deleteUser: apiDomain + '/users/delete/',
    // store table.
	storeTable : apiDomain + '/table/store',
	storeSeats : apiDomain + '/seat/store',


	deleteTables : apiDomain + '/table/delete/',
	
	deleteSeat : apiDomain + '/seat/delete/',

	//mark attendance
	markAttendance: apiDomain + '/attendance/edit',

	//state List
	states: apiDomain + '/location/states',
	//cities List 
	cities: apiDomain + '/location/cities',
	//countries List 
	countries: apiDomain + '/location/countries',

	//search cities List 
	searchCities: apiDomain + '/location/search/cities',

	//search countries List 
	searchStates: apiDomain + '/location/search/states',

	//registranst count 
	registrantsCount: apiDomain + '/event_registrants/show/',

   //invite search.
   userInviteSearch : apiDomain + '/users/invite/search/user',
  
   storeInviteUser : apiDomain + '/users/invite/send',

   eventInviteUser : apiDomain + '/users/invite/event',

   courseInviteUser : apiDomain + '/users/invite/course/user',

   eventInvitedUserList : apiDomain + '/users/invite/event/user',

   enrollUser : apiDomain + '/users/invite/enroll',

   withDrawCourse : apiDomain + '/users/invite/withdraw',

};