//home
import Home from './components/home/Home.vue';

//login
import Login from './components/login/view.vue';

//profile
import Profile from './components/profile/Profile.vue';
import ProfileWrapper from './components/profile/ProfileWrapper.vue';
import EditProfile from './components/profile/edit-profile/EditProfile.vue';

//update password
import EditPassword from './components/profile/edit-password/EditPassword.vue';

//Registration
import Registration from "./components/registration/view.vue";

//invitation
import Invite from "./components/Invitation/view.vue";

//invite Courses
import CourseInvitation from "./components/course/invite.vue";

//invite Courses
import EventInvitation from "./components/event/invite.vue";

//dashboard
import Dashboard from "./components/dashboard/view.vue";

//event
import Event from "./components/event/view.vue";

//employee
import Employee from "./components/employee/view.vue";

//food
import Food from "./components/food/view.vue";

//giveaway
import GiveAway from "./components/giveaway/view.vue";

//hotel
import Hotel from "./components/hotel/view.vue";

//course
import Course from "./components/course/view.vue";

//users
import Users from "./components/users/Users.vue";
import UsersWrapper from "./components/users/UsersWrapper.vue";

//registarnts
import Registrants from "./components/registrants/view.vue";

//seating
import Seating from "./components/seating/view.vue";


export default [{
		path: '/',
		name: 'index',
		component: Dashboard,
		meta: {}
	},
	{
		path: '/login',
		name: 'login',
		component: Login,
		meta: {
			requiresGuest: true
		}
	},
	{
		path: '/registration/:type',
		name: 'registration',
		component: Registration,
		meta: {
			requiresGuest: true
		}
	},
	{
		path: '/profile',
		component: ProfileWrapper,
		children: [{
				path: '',
				name: 'profile',
				component: Profile,
				meta: {
					requiresAuth: true
				}
			},
			{
				path: 'edit-profile',
				name: 'profile.editProfile',
				component: EditProfile,
				meta: {
					requiresAuth: true
				}
			},
			{
				path: 'edit-password',
				name: 'profile.editPassword',
				component: EditPassword,
				meta: {
					requiresAuth: true
				}
			},
			{
				path: '*',
				redirect: {
					name: 'profile'
				}
			}
		]
	},
	{
		path: '/dashboard',
		component: Dashboard,
		children: [{
				path: '',
				name: 'dashboard',
				component: Dashboard,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: true,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: true,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'dashboard'
				}
			}
		]
	},
	{
		path: '/users/:id',
		component: UsersWrapper,
		children: [{
				path: '',
				name: 'users',
				component: Users,
				meta: {
					requiresAuth: true
				}
			},
			{
				path: '*',
				redirect: {
					name: 'users'
				}
			}
		]
	},
	{
		path: '/events',
		component: Event,
		children: [{
				path: '',
				name: 'event',
				component: Event,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'event'
				}
			}
		]
	},
	{
		path: '/courses',
		component: Course,
		children: [{
			path: '',
			name: 'courses',
			component: Course,
			meta: {
				requiresAuth: true,
				permissions: [
					{
						role: "admin",
						access: true,
						redirect: "login"
					},
					{
					  role: "external_user",
					  access: false,
					  redirect: "login"
					},
					{
						role: "internal_user",
						access: false,
						redirect: "login"
					}
				  ]
			}
		}]
	},
	{
		path: '/event/course/:id',
		component: Course,
		children: [{
			path: '',
			name: 'eventcourse',
			component: Course,
			meta: {
				requiresAuth: true,
				permissions: [
					{
						role: "admin",
						access: true,
						redirect: "login"
					},
					{
					  role: "external_user",
					  access: false,
					  redirect: "login"
					},
					{
						role: "internal_user",
						access: false,
						redirect: "login"
					}
				  ]
			}
		}]
	},
	{
		path: '/event/invite/:id',
		component: Invite,
		children: [{
				path: '',
				name: 'inviteEvent',
				component: Invite,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'inviteEvent'
				}
			}
		]
	},
	{
		path: '/course/invite/:id',
		component: Invite,
		children: [{
				path: '',
				name: 'inviteCourse',
				component: Invite,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'inviteCourse'
				}
			}
		]
	},
	{
		path: '/event/food/:id',
		component: Food,
		children: [{
				path: '',
				name: 'eventFood',
				component: Food,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'food'
				}
			}
		]
	},
	{
		path: '/course/food/:id',
		component: Food,
		children: [{
				path: '',
				name: 'courseFood',
				component: Food,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'food'
				}
			}
		]
	},
	{
		path: '/event/giveaways/:id',
		component: GiveAway,
		children: [{
				path: '',
				name: 'eventGiveaways',
				component: GiveAway,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'giveaways'
				}
			}
		]
	},
	{
		path: '/course/giveaways/:id',
		component: GiveAway,
		children: [{
				path: '',
				name: 'courseGiveaways',
				component: GiveAway,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'giveaways'
				}
			}
		]
	},
	{
		path: '/event/hotel/:id',
		component: Hotel,
		children: [{
				path: '',
				name: 'eventhotel',
				component: Hotel,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'hotel'
				}
			}
		]
	},
	{
		path: '/course/hotel/:id',
		component: Hotel,
		children: [{
				path: '',
				name: 'coursehotel',
				component: Hotel,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'hotel'
				}
			}
		]
	},
	{
		path: '/event/registrants/:id',
		component: Registrants,
		children: [{
				path: '',
				name: 'eventRegistrants',
				component: Registrants,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'eventRegistrants'
				}
			}
		]
	},
	{
		path: '/course/registrants/:id',
		component: Registrants,
		children: [{
				path: '',
				name: 'courseRegistrants',
				component: Registrants,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'courseRegistrants'
				}
			}
		]
	},
	{
		path: '/event/seating/:id',
		component: Seating,
		children: [{
				path: '',
				name: 'eventSeating',
				component: Seating,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'eventSeating'
				}
			}
		]
	},
	{
		path: '/course/seating/:id',
		component: Seating,
		children: [{
				path: '',
				name: 'courseSeating',
				component: Seating,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'courseSeating'
				}
			}
		]
	},
	{
		path: '/user/course/invite/:id',
		component: CourseInvitation,
		children: [{
				path: '',
				name: 'courseInvitation',
				component: CourseInvitation,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: false,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: true,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: true,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'courseInvitation'
				}
			}
		]
	},
	{
		path: '/user/events/invite/:id',
		component: EventInvitation,
		children: [{
				path: '',
				name: 'eventInvitation',
				component: EventInvitation,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: false,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: true,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: true,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'eventInvitation'
				}
			}
		]
	},
	{
		path: '/employee',
		component: Employee,
		children: [{
				path: '',
				name: 'employee',
				component: Employee,
				meta: {
					requiresAuth: true,
					permissions: [
						{
							role: "admin",
							access: true,
							redirect: "login"
						},
						{
						  role: "external_user",
						  access: false,
						  redirect: "login"
						},
						{
							role: "internal_user",
							access: false,
							redirect: "login"
						}
					  ]
				}
			},
			{
				path: '*',
				redirect: {
					name: 'employee'
				}
			}
		]
	}
];