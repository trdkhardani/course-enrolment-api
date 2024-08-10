# Course Enrolment API

## Description
This mini-project is inspired by the system of course enrolment in the college where I study.

## Endpoints
### GET http://<BASE_URL>/api/login
Used for logging in. If the credentials are correct, it will show the **Bearer Token** to be used in **authentication**.

### GET http://<BASE_URL>/api/student
Used for student users to see their personal information. Consists of:
* Student Name
* Student ID Number
* Advisor Name
* Previous Semester Credits Total
* Previous Semester Courses and Grades
* Previous Semester GPA
* Current Semester
* Current Semester Credits Limits (Based on the previous semester GPA)

### POST http://<BASE_URL>/api/student/take-course
Used for student users to take courses on their own in their current semester. Will limit the student's maximum credits by given credits limit.

### DELETE http://<BASE_URL>/api/student/drop-course/{course_id}
Used for student users to drop courses on their own in their current semester.

### GET http://<BASE_URL>/api/student/current-courses
Used for student users to see their currently taken or enrolled courses.

### GET http://<BASE_URL>/api/student/course-detail/{course_id}
Used for student users to see detailed course information. Consists of:
* Course Name
* Course Class (A, B, etc.)
* Course Status (Open, Closed, or Full)
* Total Enrolled Students and Course/Class Capacity or Seats
* Enrolled Students Data (Student name and ID number)

### GET http://<BASE_URL>/api/advisor
Used for advisor users to see their personal information. Consists of:
* Advisor Name
* Advisor ID Number
* Advisor's Students (Student name and ID number)

### GET http://<BASE_URL>/api/advisor/student-detail/{student_id}
Used for advisor users to see their student personal information. Consists of:
* Student Name
* Student ID Number
* Previous Semester GPA
* Current Semester
* Current Semester Credits Limits (Based on the previous semester GPA)

### PATCH http://<BASE_URL>/api/advisor/accept-student-courses/{student_id}
Used for advisor users to accept their student taken courses (enroll their student).

### PATCH http://<BASE_URL>/api/advisor/cancel-student-courses/{student_id}
Used for advisor users to cancel their student enrolled courses (unenroll their student).

### POST http://<BASE_URL>/api/advisor/take-student-course/{student_id}
Used for advisor users to take a course for their student. Has the ability to bypass student's given credits limit, but the maximum credits is 24.

### DELETE http://<BASE_URL>/api/advisor/drop-student-course/{student_id}/{course_id}
Used for advisor users to drop a course for their student.

## To-Do
- [ ] Refactor
- [ ] Add missing error handlings
- [ ] More comprehensive docs
