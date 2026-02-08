import { http } from '@/shared/api/http';
import type { CalendarEventInput, CalendarTemplate, Cohort, CohortRuleInput, StudentLookup } from '@/shared/types/admin';

export async function listAdminCalendars(): Promise<CalendarTemplate[]> {
  const { data } = await http.get<{ data: CalendarTemplate[] }>('/admin/calendar-templates');
  return data.data;
}

export async function createAdminCalendar(payload: {
  name: string;
  description?: string;
  is_active?: boolean;
  events: CalendarEventInput[];
}): Promise<CalendarTemplate> {
  const { data } = await http.post<{ data: CalendarTemplate }>('/admin/calendar-templates', payload);
  return data.data;
}

export async function updateAdminCalendar(templateId: number, payload: {
  name?: string;
  description?: string;
  is_active?: boolean;
  events?: CalendarEventInput[];
}): Promise<CalendarTemplate> {
  const { data } = await http.put<{ data: CalendarTemplate }>(`/admin/calendar-templates/${templateId}`, payload);
  return data.data;
}

export async function listAdminCohorts(): Promise<Cohort[]> {
  const { data } = await http.get<{ data: Cohort[] }>('/admin/cohorts');
  return data.data;
}

export async function createAdminCohort(payload: {
  name: string;
  course_type?: string;
  enrollment_type?: string;
  calendar_template_id?: number | null;
  capacity?: number | null;
  starts_at?: string;
  ends_at?: string;
  auto_assign?: boolean;
  is_active?: boolean;
  rules?: CohortRuleInput[];
}): Promise<Cohort> {
  const { data } = await http.post<{ data: Cohort }>('/admin/cohorts', payload);
  return data.data;
}

export async function updateAdminCohort(cohortId: number, payload: {
  name?: string;
  course_type?: string;
  enrollment_type?: string;
  calendar_template_id?: number | null;
  capacity?: number | null;
  starts_at?: string;
  ends_at?: string;
  auto_assign?: boolean;
  is_active?: boolean;
  rules?: CohortRuleInput[];
}): Promise<Cohort> {
  const { data } = await http.put<{ data: Cohort }>(`/admin/cohorts/${cohortId}`, payload);
  return data.data;
}

export async function findStudentByEmail(email: string): Promise<StudentLookup> {
  const { data } = await http.get<{ data: { student: StudentLookup } }>(`/admin/students/by-email?email=${encodeURIComponent(email)}`);
  return data.data.student;
}

export async function assignStudentToCohort(studentId: number, payload: {
  cohort_id: number;
  reason?: string;
  force?: boolean;
}): Promise<void> {
  await http.post(`/admin/students/${studentId}/cohort-assignment`, payload);
}

export async function assignStudentCalendar(studentId: number, payload: {
  calendar_template_id: number;
  assigned_start_date?: string;
}): Promise<void> {
  await http.post(`/admin/students/${studentId}/calendar-assignment`, payload);
}
