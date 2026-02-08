import { useMemo } from 'react';
import { Link } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { useAuth } from '@/features/auth/AuthProvider';
import {
  getStudentCalendar,
  getStudentProgress,
  listStudentPayments,
  listStudentSubjects,
} from '@/shared/api/student-api';
import type { StudentCalendarEvent, StudentPayment, StudentSubject } from '@/shared/types/student';
import { Card } from '@/shared/ui/Card';
import { ProgressRing } from '@/shared/ui/ProgressRing';

const LEARNING_FACTS = [
  'Estudiar en bloques de 25 minutos suele mejorar la retencion.',
  'Explicar un tema en voz alta ayuda a detectar lagunas de aprendizaje.',
  'Alternar materias reduce la fatiga mental en sesiones largas.',
  'Dormir bien antes de un examen mejora la memoria de largo plazo.',
  'Tomar notas con tus propias palabras acelera la comprension.',
  'Practicar con preguntas cortas frecuentes es mas efectivo que releer.',
];

function getNextPaymentEvent(events: StudentCalendarEvent[]): StudentCalendarEvent | null {
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const next = events
    .filter((event) => event.is_payment)
    .map((event) => ({ event, date: new Date(event.due_date) }))
    .filter((item) => !Number.isNaN(item.date.getTime()) && item.date >= today)
    .sort((a, b) => a.date.getTime() - b.date.getTime())[0];

  return next?.event ?? null;
}

function isApproved(status: string, statusValue?: number | null) {
  if (statusValue === 2) {
    return true;
  }

  const normalized = status.toLowerCase().trim();
  return normalized === 'approved' || normalized === 'aprobada' || normalized === 'aprobado';
}

function formatDate(value: string) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return date.toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}

function formatMoney(amount: number, currency = 'MXN') {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency,
    maximumFractionDigits: 2,
  }).format(amount);
}

export function StudentDashboardPage() {
  const { session } = useAuth();

  const { data: progress, isLoading: progressLoading } = useQuery({
    queryKey: ['student-progress'],
    queryFn: getStudentProgress,
  });

  const { data: subjects, isLoading: subjectsLoading } = useQuery({
    queryKey: ['student-subjects'],
    queryFn: listStudentSubjects,
  });

  const { data: payments, isLoading: paymentsLoading } = useQuery({
    queryKey: ['student-payments'],
    queryFn: listStudentPayments,
  });

  const { data: calendar, isLoading: calendarLoading } = useQuery({
    queryKey: ['student-calendar'],
    queryFn: getStudentCalendar,
  });

  const randomFact = useMemo(
    () => LEARNING_FACTS[Math.floor(Math.random() * LEARNING_FACTS.length)],
    []
  );

  const nextPayment = useMemo(() => getNextPaymentEvent(calendar?.data?.events ?? []), [calendar?.data?.events]);

  const recentPayments = useMemo(() => {
    const items = [...(payments ?? [])];
    return items
      .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
      .slice(0, 3);
  }, [payments]);

  const approvedCountFromList = useMemo(
    () => (subjects ?? []).filter((item) => isApproved(item.status, item.status_value)).length,
    [subjects]
  );

  const pendingSubjects = useMemo(
    () => (subjects ?? []).filter((item) => !isApproved(item.status, item.status_value)).slice(0, 4),
    [subjects]
  );

  return (
    <div className="grid gap-4 lg:grid-cols-12">
      <div className="lg:col-span-12">
        <Card>
          <div className="flex flex-wrap items-center justify-between gap-4">
            <div>
              <p className="text-xs uppercase tracking-wide text-slate-500">Panel estudiante</p>
              <h1 className="mt-1 text-xl font-bold text-slate-900">
                Bienvenido, {session?.user.first_name} {session?.user.surnames}
              </h1>
              <p className="mt-1 text-sm text-slate-600">Avance general basado en materias aprobadas.</p>
            </div>
            <div className="flex flex-wrap gap-2">
              <Link
                className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                to="/app/subjects"
              >
                Ver materias
              </Link>
              <Link
                className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                to="/app/payments"
              >
                Ver pagos
              </Link>
              <Link
                className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                to="/app/calendar"
              >
                Ver calendario
              </Link>
            </div>
          </div>
        </Card>
      </div>

      <div className="lg:col-span-4">
        <Card>
          <div className="flex h-full flex-col items-center justify-center gap-3 text-center">
            {progressLoading ? (
              <p className="text-sm text-slate-500">Cargando avance...</p>
            ) : (
              <>
                <ProgressRing value={progress?.percentage ?? 0} />
                <p className="text-sm text-slate-600">
                  {progress?.approved_count ?? approvedCountFromList} de {progress?.total_count ?? subjects?.length ?? 0} materias aprobadas
                </p>
              </>
            )}
          </div>
        </Card>
      </div>

      <div className="lg:col-span-4">
        <Card>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">Resumen rapido</h2>
          <div className="mt-3 grid gap-2 text-sm text-slate-700">
            <div className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
              Aprobadas: <span className="font-semibold">{progress?.approved_count ?? approvedCountFromList}</span>
            </div>
            <div className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
              Habilitadas: <span className="font-semibold">{progress?.enabled_count ?? 0}</span>
            </div>
            <div className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
              Inhabilitadas: <span className="font-semibold">{progress?.disabled_count ?? 0}</span>
            </div>
            <div className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
              Total materias: <span className="font-semibold">{progress?.total_count ?? subjects?.length ?? 0}</span>
            </div>
          </div>
        </Card>
      </div>

      <div className="lg:col-span-4">
        <Card>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">Proximo pago</h2>
          {calendarLoading ? (
            <p className="mt-3 text-sm text-slate-500">Cargando calendario...</p>
          ) : nextPayment ? (
            <div className="mt-3 rounded-xl border border-amber-300 bg-amber-50 p-3">
              <p className="font-semibold text-amber-900">{nextPayment.title}</p>
              <p className="mt-1 text-sm text-amber-800">Fecha limite: {formatDate(nextPayment.due_date)}</p>
            </div>
          ) : (
            <p className="mt-3 text-sm text-slate-600">Sin pagos pendientes en calendario.</p>
          )}
          <Link className="mt-3 inline-block text-sm font-semibold text-brand-700" to="/app/calendar">
            Ir al calendario
          </Link>
        </Card>
      </div>

      <div className="lg:col-span-4">
        <Card>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">Pagos recientes</h2>
          {paymentsLoading ? (
            <p className="mt-3 text-sm text-slate-500">Cargando pagos...</p>
          ) : recentPayments.length > 0 ? (
            <ul className="mt-3 grid gap-2 text-sm">
              {recentPayments.map((payment: StudentPayment) => (
                <li key={payment.id} className="rounded-lg border border-slate-200 p-2">
                  <p className="font-semibold text-slate-800">{payment.provider} · {payment.status}</p>
                  <p className="text-slate-600">{formatMoney(payment.amount, payment.currency ?? 'MXN')}</p>
                  <p className="text-xs text-slate-500">{formatDate(payment.created_at)}</p>
                </li>
              ))}
            </ul>
          ) : (
            <p className="mt-3 text-sm text-slate-600">Aun no hay pagos registrados.</p>
          )}
          <Link className="mt-3 inline-block text-sm font-semibold text-brand-700" to="/app/payments">
            Ver historial completo
          </Link>
        </Card>
      </div>

      <div className="lg:col-span-4">
        <Card>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">Materias por completar</h2>
          {subjectsLoading ? (
            <p className="mt-3 text-sm text-slate-500">Cargando materias...</p>
          ) : pendingSubjects.length > 0 ? (
            <ul className="mt-3 grid gap-2 text-sm">
              {pendingSubjects.map((subject: StudentSubject) => (
                <li key={subject.subject_id} className="rounded-lg border border-slate-200 p-2">
                  <p className="font-semibold text-slate-800">{subject.subject_name}</p>
                  <p className="text-slate-600">Estado: {subject.status}</p>
                </li>
              ))}
            </ul>
          ) : (
            <p className="mt-3 text-sm text-emerald-700">No tienes materias pendientes. Excelente avance.</p>
          )}
          <Link className="mt-3 inline-block text-sm font-semibold text-brand-700" to="/app/subjects">
            Abrir materias
          </Link>
        </Card>
      </div>

      <div className="lg:col-span-4">
        <Card>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">Dato curioso</h2>
          <p className="mt-3 text-sm text-slate-700">{randomFact}</p>
        </Card>
      </div>
    </div>
  );
}
