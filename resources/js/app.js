import Alpine from 'alpinejs';

import workshopPicker from './dashboard/workshopPicker';
import tutorial from './dashboard/tutorial';
import mobileTutorial from './dashboard/mobileTutorial';
import statistics from './admin/statistics';
import studentsOverview from './admin/studentsOverview';
import studentEdit from './admin/studentEdit';
import workshopsOverview from './admin/workshopsOverview';
import workshopMomentBookings from './admin/workshopMomentBookings';
import classesOverview from './admin/classesOverview';
import registerForm from './auth/registerForm';

window.Alpine = Alpine;

// student dashboard
Alpine.data('workshopPicker', workshopPicker);
Alpine.data('mobileTutorial', mobileTutorial);
Alpine.store('tutorial', tutorial);

// admin
Alpine.data('statistics', statistics);
Alpine.data('studentsOverview', studentsOverview);
Alpine.data('studentEdit', studentEdit);
Alpine.data('workshopsOverview', workshopsOverview);
Alpine.data('workshopMomentBookings', workshopMomentBookings);
Alpine.data('classesOverview', classesOverview);

// registreren
Alpine.data('registerForm', registerForm);

Alpine.start();
