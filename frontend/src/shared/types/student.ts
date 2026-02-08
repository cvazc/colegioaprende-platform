export type StudentProgress = {
  percentage: number;
  approved_count: number;
  enabled_count: number;
  disabled_count: number;
  total_count: number;
};

export type StudentProgressResponse = {
  data: {
    overall: StudentProgress;
  };
};

export type StudentSubject = {
  subject_id: number;
  subject_name: string;
  status: string;
  status_value?: number | null;
  score: number;
};

export type StudentSubjectsResponse = {
  data: StudentSubject[];
};

export type StudentPayment = {
  id: number;
  provider: string;
  status: string;
  amount: number;
  currency?: string;
  created_at: string;
};

export type StudentPaymentsResponse = {
  data: StudentPayment[];
};

export type StudentCalendarEvent = {
  id: number;
  title: string;
  due_date: string;
  is_payment: boolean;
};

export type StudentCalendarData = {
  calendar_name: string;
  events: StudentCalendarEvent[];
};

export type StudentCalendarResponse = {
  data: StudentCalendarData | null;
  message?: string;
};
