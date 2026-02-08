import { http } from '@/shared/api/http';
import type {
  StudentCalendarResponse,
  StudentPaymentsResponse,
  StudentProgressResponse,
  StudentSubjectsResponse,
} from '@/shared/types/student';

export async function getStudentProgress() {
  const { data } = await http.get<StudentProgressResponse>('/student/progress');
  return data.data.overall;
}

export async function listStudentSubjects() {
  const { data } = await http.get<StudentSubjectsResponse>('/student/subjects');
  return data.data;
}

export async function listStudentPayments() {
  const { data } = await http.get<StudentPaymentsResponse>('/student/payments');
  return data.data;
}

export async function getStudentCalendar() {
  const { data } = await http.get<StudentCalendarResponse>('/student/calendar');
  return data;
}
