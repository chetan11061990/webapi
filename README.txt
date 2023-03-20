------------------SOFTWARE REQUIREMENTS--------------
Programming Language : PHP 8.1
Server : apache
Database : postgresql 

----------------.env------------------
Database configuration need to add in .env file 
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=


---------------DATABASE------------------------
Postgres

DATABASE : company
Schema : public

CREATE TABLE public.department (
	id serial4 not null unique,
	name varchar(80) default null,
	PRIMARY KEY(id)
)


CREATE TABLE public.employee(  
	id serial4 not null unique,  
	firstname varchar(80) default null,
	lastname varchar(80) default null,
	deptid int,  
	PRIMARY KEY(id),  
	CONSTRAINT fk_dept  
	FOREIGN KEY(deptid)   
	REFERENCES public.department(id)  
); 

CREATE TABLE public.employee_address(  
	id serial4 not null unique,  
	address text default null,
	empid int,  
	PRIMARY KEY(id),  
	CONSTRAINT fk_emp  
	FOREIGN KEY(empid)   
	REFERENCES public.employee(id)  
);

CREATE TABLE public.employee_contactno(  
	id serial4 not null unique,  
	contactno varchar(18) null,
	empid int,  
	PRIMARY KEY(id),  
	CONSTRAINT fk_emp  
	FOREIGN KEY(empid)   
	REFERENCES public.employee(id)  
);  


------------------API-------------------------

Departments :
i. Create
URL : http://localhost/webapi/department/

METHOD : POST

Request Params:
	department // Name of department

Response: 
	i. Success
	status_code : HTTP/1.1 201 Created'
	body : 'success' => 'Department created succesfully',

	ii. Validation error
	status_code : HTTP/1.1 422 Invalid Entity
	body : 'error' => Invalid Input

eg.
Input : 
{
    "department":"IT"
}

Output:


Employee : 
i. Create : 
URL : http://localhost/webapi/employee/

METHOD : POST 

Request Params:
	firstname
	lastname
	deptid //Department id
	addresses // Array of addresses
	contactnos // Array of contactnos

Response: 
	i. Success
	status_code : HTTP/1.1 201 Created'
	body : 'success' => 'Employee data created succesfully',

	ii. Validation error
	status_code : HTTP/1.1 422 Invalid Entity
	body : 'error' => Invalid Input

eg.
Input : 
{
    "firstname":"XYZ",
    "lastname":"EFG",
    "deptid":1,
    "address":{
        "address1":"Kalyan,Maharashtra",
        "address2":"Mumbai,Maharashtra"
    },
    "contactnos":{
        "contactno1":"1234567890",
        "contactno2":"1234585248"
    }
}
Output:
{"success":"Employee data added succesfully"}


ii. Edit : 
URL : http://localhost/webapi/employee/{empId}/

METHOD : PUT 

Request Params:
	firstname
	lastname
	deptid //Department id
	addresses // Array of addresses
	contactnos // Array of contactnos
	empId // Needs to be added in URL

Response: 
	i. Success
	status_code : HTTP/1.1 201 Created'
	body : 'success' => 'Employee data updated succesfully',

	ii. Validation error
	status_code : HTTP/1.1 422 Invalid Entity
	body : 'error' => Invalid Input

	status_code : HTTP/1.1 422 Invalid Entity
	body : 'error' => Emp Id is missing

	iii.No data Found
	status_code : HTTP/1.1 200
	body : 'error' => 'No Data found',

eg.
Input : 
{
    "firstname":"XYZ",
    "lastname":"EFG",
    "deptid":1,
    "address":{
        "address1":"Kalyan,Maharashtra",
        "address2":"Mumbai,Maharashtra"
    },
    "contactnos":{
        "contactno1":"1234567890",
        "contactno2":"1234585248"
    }
}
Output:
{"success":"Employee data updated succesfully"}



ii. Delete : 
URL : http://localhost/webapi/employee/{empId}/

METHOD : DELETE 

Request Params:
	empId // Needs to be added in URL


Response: 
	i. Success
	status_code : HTTP/1.1 200 OK
	body : 'success' => 'Employee data deleted succesfully',

	ii. Validation error
	status_code : HTTP/1.1 422 Invalid Entity
	body : 'error' => Emp Id is missing

	iii.No data Found
	status_code : HTTP/1.1 200
	body : 'error' => 'No Data found',


iii.View:
URL : http://localhost/webapi/employee/

METHOD : GET 

Request Params:
	empId

Response: 
	i. Success
	status_code : HTTP/1.1 200 OK
	body : 'data' => {employee data},

	ii. Validation error
	status_code : HTTP/1.1 422 Invalid Entity
	body : 'error' => Emp Id is missing

	iii.No data Found
	status_code : HTTP/1.1 200
	body : 'error' => 'No Data found',

eg.
Input: 
URL : http://localhost/webapi/employee/12/

Output:
{
    "data": {
        "id": 12,
        "firstname": "XYZ",
        "lastname": "EFG",
        "department": "IT",
        "addresses": {
            "address1": "Pune,Maharashtra",
            "address2": "Kalyan,Maharashtra"
        },
        "contactnos": {
            "contactno1": "121151515",
            "contactno2": "123455878",
            "contactno3": "124455878"
        }
    }
}

iv.Search : 
URL : http://localhost/webapi/employee/

METHOD : GET 

Request Params:
	search

Response: 
	i. Success
	status_code : HTTP/1.1 200 OK
	body : 'data' => {employee data},

	ii. Validation error
	status_code : 422 Invalid Entity
	body : 'error' => Invalid Input

	iii.No data Found
	status_code : HTTP/1.1 200 OK
	body : 'error' => 'No Data found',

eg.
Input: 
URL : http://localhost/webapi/employee/

search : Pune

Output:
{
    "data": [
        {
            "id": 26,
            "firstname": "Chetan",
            "lastname": "S",
            "department": "IT",
            "addresses": {
                "address1": "Solapur,Maharashtra",
                "address2": "Pune,Mumbai"
            },
            "contactnos": {
                "contactno1": "213122313",
                "contactno2": "212313131",
                "contactno3": "123234434"
            }
        },
        {
            "id": 27,
            "firstname": "ABC",
            "lastname": "EFG",
            "department": "IT",
            "addresses": {
                "address1": "Nashik,Maharashtra",
                "address2": "Pune,Mumbai"
            },
            "contactnos": {
                "contactno1": "213122313",
                "contactno2": "212313131",
                "contactno3": "123234434"
            }
        }
    ]
}

For Invalid URL which is not related to department or employee
Error : 404 Not Found
