import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '@/features/auth/AuthProvider';
import { Button } from '@/shared/ui/Button';
import { BrandLogo } from '@/shared/ui/BrandLogo';
import type { StudentUser } from '@/shared/types/auth';

function getInitials(firstName?: string, surnames?: string) {
  const first = (firstName ?? '').trim().charAt(0);
  const last = (surnames ?? '').trim().charAt(0);
  return `${first}${last}`.toUpperCase() || 'EA';
}

export function StudentLayout() {
  const { session, logout } = useAuth();
  const navigate = useNavigate();
  const studentUser = session?.role === 'student' ? (session.user as StudentUser) : null;
  const photoUrl = studentUser?.profile_photo_url ?? null;
  const initials = getInitials(studentUser?.first_name, studentUser?.surnames);

  return (
    <div className="min-h-screen bg-slate-100">
      <header className="border-b border-brand-100 bg-white">
        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3">
          <div className="flex items-center gap-3">
            <BrandLogo className="h-10" />
            {photoUrl ? (
              <img
                src={photoUrl}
                alt="Foto de perfil"
                className="h-10 w-10 rounded-full border border-slate-200 object-cover"
              />
            ) : (
              <div className="flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 bg-slate-100 text-xs font-bold text-slate-700">
                {initials}
              </div>
            )}
            <div>
              <p className="text-xs uppercase tracking-wide text-slate-500">Panel Estudiante</p>
              <p className="text-sm font-semibold text-slate-800">{session?.user.first_name} {session?.user.surnames}</p>
            </div>
          </div>
          <div className="flex items-center gap-2 text-sm">
            <NavLink to="/app">Dashboard</NavLink>
            <NavLink to="/app/subjects">Materias</NavLink>
            <NavLink to="/app/payments">Pagos</NavLink>
            <NavLink to="/app/calendar">Calendario</NavLink>
            <NavLink to="/app/cohort">Cohorte</NavLink>
            <NavLink to="/app/onboarding">Perfil</NavLink>
            <Button
              variant="secondary"
              onClick={async () => {
                await logout();
                navigate('/login', { replace: true });
              }}
            >
              Salir
            </Button>
          </div>
        </div>
      </header>
      <main className="mx-auto max-w-7xl px-4 py-6">
        <Outlet />
      </main>
    </div>
  );
}
