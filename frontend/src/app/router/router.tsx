import { createBrowserRouter, Navigate } from 'react-router-dom';
import { PublicLayout } from '@/app/layouts/PublicLayout';
import { StudentLayout } from '@/app/layouts/StudentLayout';
import { AdminLayout } from '@/app/layouts/AdminLayout';
import { HomePage } from '@/features/public/HomePage';
import { RegisterPage } from '@/features/public/RegisterPage';
import { LoginPage } from '@/features/public/LoginPage';
import { NotFoundPage } from '@/features/public/NotFoundPage';
import { RequireAuth, RequireEmployeePermission } from '@/features/auth/RouteGuards';
import { StudentDashboardPage } from '@/features/dashboard/StudentDashboardPage';
import { StudentSubjectsPage } from '@/features/students/StudentSubjectsPage';
import { StudentPaymentsPage } from '@/features/students/StudentPaymentsPage';
import { StudentCalendarPage } from '@/features/students/StudentCalendarPage';
import { StudentCohortPage } from '@/features/students/StudentCohortPage';
import { StudentOnboardingPage } from '@/features/students/StudentOnboardingPage';
import { AdminDashboardPage } from '@/features/admin/AdminDashboardPage';
import { AdminProspectsPage } from '@/features/admin/AdminProspectsPage';
import { AdminStudentsPage } from '@/features/admin/AdminStudentsPage';
import { AdminCohortsPage } from '@/features/admin/AdminCohortsPage';
import { AdminCalendarsPage } from '@/features/admin/AdminCalendarsPage';
import { AdminPaymentsPage } from '@/features/admin/AdminPaymentsPage';
import { AdminEmployeesPage } from '@/features/admin/AdminEmployeesPage';

export const router = createBrowserRouter([
  {
    path: '/',
    element: <PublicLayout />,
    children: [
      { index: true, element: <HomePage /> },
      { path: 'register', element: <RegisterPage /> },
      { path: 'login', element: <LoginPage /> },
    ],
  },
  {
    element: <RequireAuth role="student" />,
    children: [
      {
        path: '/app',
        element: <StudentLayout />,
        children: [
          { index: true, element: <StudentDashboardPage /> },
          { path: 'subjects', element: <StudentSubjectsPage /> },
          { path: 'payments', element: <StudentPaymentsPage /> },
          { path: 'calendar', element: <StudentCalendarPage /> },
          { path: 'cohort', element: <StudentCohortPage /> },
          { path: 'onboarding', element: <StudentOnboardingPage /> },
        ],
      },
    ],
  },
  {
    element: <RequireAuth role="employee" />,
    children: [
      {
        path: '/admin',
        element: <AdminLayout />,
        children: [
          { index: true, element: <AdminDashboardPage /> },
          { path: 'prospects', element: <AdminProspectsPage /> },
          { path: 'students', element: <AdminStudentsPage /> },
          { path: 'cohorts', element: <AdminCohortsPage /> },
          { path: 'calendars', element: <AdminCalendarsPage /> },
          { path: 'payments', element: <AdminPaymentsPage /> },
          {
            element: <RequireEmployeePermission ability="manage_employees" />,
            children: [{ path: 'employees', element: <AdminEmployeesPage /> }],
          },
        ],
      },
    ],
  },
  { path: '*', element: <NotFoundPage /> },
  { path: '/dashboard', element: <Navigate to="/app" replace /> },
  { path: '/panel-admin', element: <Navigate to="/admin" replace /> },
]);
