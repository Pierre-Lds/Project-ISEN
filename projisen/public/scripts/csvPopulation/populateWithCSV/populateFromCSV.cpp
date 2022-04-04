//
// Created by rouxt on 04/03/2022.
//

#include <string>
#include <vector>
#include <stdexcept>
#include <fstream>
#include <sstream>
#include <iostream>
#include "populateFromCSV.h"
#include <sstream>
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <cstring>

#include "../define.h"

using namespace std;


#include <istream>
#include <string>
#include <vector>

enum class CSVState {
    UnquotedField,
    QuotedField,
    QuotedQuote
};

std::vector<std::string> readCSVRow(const std::string &row) {
    CSVState state = CSVState::UnquotedField;
    std::vector<std::string> fields {""};
    size_t i = 0; // index of the current field
    for (char c : row) {
        switch (state) {
            case CSVState::UnquotedField:
                switch (c) {
                    case ';': // end of field
                        fields.push_back(""); i++;
                        break;
                    case '"': state = CSVState::QuotedField;
                        break;
                    default:  fields[i].push_back(c);
                        break; }
                break;
            case CSVState::QuotedField:
                switch (c) {
                    case '"': state = CSVState::QuotedQuote;
                        break;
                    default:  fields[i].push_back(c);
                        break; }
                break;
            case CSVState::QuotedQuote:
                switch (c) {
                    case ',': // , after closing quote
                        fields.push_back(""); i++;
                        state = CSVState::UnquotedField;
                        break;
                    case '"': // "" -> "
                        fields[i].push_back('"');
                        state = CSVState::QuotedField;
                        break;
                    default:  // end of quote
                        state = CSVState::UnquotedField;
                        break; }
                break;
        }
    }
    return fields;
}

/// Read CSV file, Excel dialect. Accept "quoted fields ""with quotes"""
std::vector<std::vector<std::string>> readCSV(std::istream &in) {
    std::vector<std::vector<std::string>> table;
    std::string row;
    while (!in.eof()) {
        std::getline(in, row);
        if (in.bad() || in.fail()) {
            break;
        }
        auto fields = readCSVRow(row);
        table.push_back(fields);
    }
    return table;
}

void printTwoDimensionVector(vector<vector<std::string>> vect){
    for (int i = 0; i < vect.size(); i++)
    {
        for (int j = 0; j < vect[i].size(); j++)
        {
            cout << vect[i][j];
        }
        cout << endl;
    }
}

void populateThematicTable(){
    std::filebuf fb;
    if (fb.open (POPULATETHEMATIC,std::ios::in))
    {
        std::istream is(&fb);
        vector<vector<std::string>> vect = readCSV(is);
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        try {
            // Création of a new connexion :
            driver = get_driver_instance();
            con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);
            // Connexion to the database :
            con->setSchema("projisen");
            // First step : counting how much vertices we have
            for(int i = 1; i<vect.size(); i++){
                if(vect[i][0].compare(vect[i][0].size() - 1, 1, "\r") == 0){
                    vect[i][0].erase(vect[i][0].size() - 1);
                }

                //First we check if the thematic is already in the database :
                stmt = con->createStatement();
                sql::SQLString checkDb;
                checkDb+= "SELECT id from thematic WHERE name ='";
                checkDb+= vect[i][0];
                checkDb+= "';";

                cout << checkDb<< endl;

                sql::ResultSet *resultSet = stmt->executeQuery(checkDb);
                //resultSet->next();

                if(resultSet->next()){
                   // Already in the database :
                   cout << vect[i][0] << " : Already in database" << endl;
                } else {
                    // We can push it in the database
                    stmt = con->createStatement();
                    sql::SQLString req;
                    req+= "INSERT INTO thematic (name) VALUES ( '";
                    req+= vect[i][0];
                    req+="' );";
                    cout << req << endl;
                    stmt->executeUpdate(req);
                }


            }
        }catch (sql::SQLException &e){
            cout << "#ERR : SQLException in "<< __FILE__;
            cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
            cout << " ERR" <<e.what() << endl;
        }
    }
}

void populateDomainProTable(){

    std::filebuf fb;
    if (fb.open (POPULATEDPFILE,std::ios::in))
    {
        std::istream is(&fb);
        vector<vector<std::string>> vect = readCSV(is);
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        try {
            // Création of a new connexion :
            driver = get_driver_instance();
            con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);
            // Connexion to the database :
            con->setSchema("projisen");
            for(int i = 1; i<vect.size(); i++){
                if(vect[i][0].compare(vect[i][0].size() - 1, 1, "\r") == 0){
                    vect[i][0].erase(vect[i][0].size() - 1);
                }

                // We check if it's already in our database :
                stmt = con->createStatement();
                sql::SQLString checkDb;
                checkDb+= "SELECT id from professional_domain WHERE name = '";
                checkDb+= vect[i][0];
                checkDb+= "';";

                //cout << checkDb<< endl;

                sql::ResultSet *resultSet = stmt->executeQuery(checkDb);

                if(resultSet->next()){
                    // Already in the database :
                    cout << vect[i][0] << " : Already in database" << endl;
                } else {
                    stmt = con->createStatement();
                    sql::SQLString req;
                    req+= "INSERT INTO professional_domain (name) VALUES ( '";
                    req+= vect[i][0];
                    req+="' );";
                    cout << req << endl;
                    stmt->executeUpdate(req);
                }

            }
        }catch (sql::SQLException &e){
            cout << "#ERR : SQLException in "<< __FILE__;
            cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
            cout << " ERR" <<e.what() << endl;
        }
    }

}

string createTeacherPseudo(string firstName, string lastName){
    // The username is created from the first name of the name + the full last name
    // If there is a composed first name we take all the inicial

    string response;

    // Follow the place where we are in response :

    // First name :
    for(int i = 0; i< firstName.length(); i++){
        if(i==0){
            response+=firstName[0];
        }

        if(firstName[i] == ' ' || firstName[i] == '-'){
            response+=firstName[i+1];
        }
    }

    // Last name :
    for(int i = 0; i<lastName.length(); i++){
        response+=lastName[i];
    }
    return response;
}

void populateTeacherTable(){
    std::filebuf fb;
    if (fb.open (POPULATESTAFF,std::ios::in))
    {
        std::istream is(&fb);
        vector<vector<std::string>> vect = readCSV(is);
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        try {
            // Création of a new connexion :
            driver = get_driver_instance();
            con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);
            // Connexion to the database :
            con->setSchema("projisen");
            // First step : counting how much vertices we have
            for(int i = 1; i<vect.size(); i++){
                // We check that all the data is good :
                for(int j = 0; j<vect[i].size(); j++){
                    if(vect[i][j].compare(vect[i][j].size() - 1, 1, "\r") == 0){
                        vect[i][j].erase(vect[i][j].size() - 1);
                    }
                }
                // We create the user name :
                string userName = createTeacherPseudo(vect[i][0], vect[i][1]);

                std::transform(userName.begin(), userName.end(), userName.begin(),
                               [](unsigned char c){ return std::tolower(c); });

                // We check if it's already in our database :
                stmt = con->createStatement();
                sql::SQLString checkDb;
                checkDb+= "SELECT id from staff WHERE username = '";
                checkDb+= userName;
                checkDb+= "';";



                //cout << checkDb<< endl;

                sql::ResultSet *resultSet = stmt->executeQuery(checkDb);

                if(resultSet->next()){
                    // Already in the database :
                    cout << userName << " : Already in database" << endl;
                } else {
                    stmt = con->createStatement();
                    sql::SQLString req;
                    req+= "INSERT INTO staff (first_name, last_name, password, is_admin, username, roles) VALUES ( '";
                    req+= vect[i][0];
                    req+= "', '";
                    req+= vect[i][1] += "' , 'root' , 0, '";
                    req+= userName;
                    req+= "',  '[\"ROLE_TEACHER\"]' );";
                    cout << req << endl;
                    stmt->executeUpdate(req);
                }
            }
        }catch (sql::SQLException &e){
            cout << "#ERR : SQLException in "<< __FILE__;
            cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
            cout << " ERR" <<e.what() << endl;
        }
    }
}

string createStudentUsername(string firstName, string lastName, int year){
    // 1er lettre du prénom, si prénom composé 1ere lettre de chaque prénom composé,
    // 5 lettre du nom de famille, si moins de 5 lettres on comble avec des 0, si plus on cut. Tombe à 4 si c'est un prénom composé
    // Unité d'année de diplomation (2023 -> 23)

    string response;

    int numberOfLettre = 0;

    // Follow the place where we are in response :

    // First name :
    for(int i = 0; i< firstName.length(); i++){
        if(i==0){
            response+=firstName[0];
            numberOfLettre++;
        }

        if(firstName[i] == ' ' || firstName[i] == '-'){
            response+=firstName[i+1];
            numberOfLettre++;
        }
    }

    // Last name :
    for(int i = 0; i<lastName.length(); i++){
        if(numberOfLettre < 6){
            response+=lastName[i];
            numberOfLettre++;
        }
    }

    // We add the 0 :
    for(int i=0; i<5; i++){
        if(numberOfLettre < 6){
            response+= "0";
            numberOfLettre++;
        }
    }

    // We add the year of graduation :
    int yr=year%100;

    response+= std::to_string(yr);

    return response;
}

void populateStudentTable(){
    std::filebuf fb;
    if (fb.open (POPULATESTUDENT,std::ios::in))
    {
        std::istream is(&fb);
        vector<vector<std::string>> vect = readCSV(is);
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        try {
            // Création of a new connexion :
            driver = get_driver_instance();
            con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);
            // Connexion to the database :
            con->setSchema("projisen");
            // First step : counting how much vertices we have
            for(int i = 1; i<vect.size(); i++){
                // We check that all the data is good :
                for(int j = 0; j<vect[i].size(); j++){
                    if(vect[i][j].compare(vect[i][j].size() - 1, 1, "\r") == 0){
                        vect[i][j].erase(vect[i][j].size() - 1);
                    }
                }
                // We create the user name :
                string userName = createStudentUsername(vect[i][0], vect[i][1], stoi(vect[i][3]));

                std::transform(userName.begin(), userName.end(), userName.begin(),
                               [](unsigned char c){ return std::tolower(c); });

                // We check if it's already in our database :
                stmt = con->createStatement();
                sql::SQLString checkDb;
                checkDb+= "SELECT id from student WHERE username = '";
                checkDb+= userName;
                checkDb+= "';";

                //cout << checkDb<< endl;

                sql::ResultSet *resultSet2 = stmt->executeQuery(checkDb);

                if(resultSet2->next()){
                    // Already in the database :
                    cout << userName << " : Already in database" << endl;
                } else {
                    // We get the id of the professional domain :
                    sql::ResultSet *resultSet;
                    stmt = con->createStatement();
                    sql::SQLString getDP;
                    getDP+="SELECT id FROM professional_domain WHERE name = '";
                    getDP+=vect[i][2];
                    getDP+= "' ;";
                    resultSet = stmt->executeQuery(getDP);

                    resultSet->next();
                    int dpId = stoi(resultSet->getString(1));

                    stmt = con->createStatement();
                    sql::SQLString req;
                    req+= "INSERT INTO student (username, roles, password, first_name, last_name, graduation_year, id_professional_domain_id, is_main_student)"
                          " VALUES ( '";
                    req+= userName;
                    req+= "', '[\"ROLE_STUDENT\"]', 'test' , '";
                    req+= vect[i][0];
                    req+= "', '";
                    req+= vect[i][1];
                    req+= "', ";
                    req+= vect[i][3];
                    req+= " , ";
                    req+= to_string(dpId);
                    req+= ", 0";
                    req+= " );";

                    cout << req << endl;
                    stmt->executeUpdate(req);
                }
            }
        }catch (sql::SQLException &e){
            cout << "#ERR : SQLException in "<< __FILE__;
            cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
            cout << " ERR" <<e.what() << endl;
        }
    }
}

// for string delimiter
vector<string> split (string s, string delimiter) {
    size_t pos_start = 0, pos_end, delim_len = delimiter.length();
    string token;
    vector<string> res;

    while ((pos_end = s.find (delimiter, pos_start)) != string::npos) {
        token = s.substr (pos_start, pos_end - pos_start);
        pos_start = pos_end + delim_len;
        res.push_back (token);
    }

    res.push_back (s.substr (pos_start));
    return res;
}

void populateProjectTable(){
    std::filebuf fb;
    if (fb.open (POPULATEPROJECT,std::ios::in))
    {
        std::istream is(&fb);
        vector<vector<std::string>> vect = readCSV(is);
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        try {
            // Création of a new connexion :
            driver = get_driver_instance();
            con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);
            // Connexion to the database :
            con->setSchema("projisen");
            // First step : counting how much vertices we have
            for(int i = 1; i<vect.size(); i++){
                // We check that all the data is good :
                for(int j = 0; j<vect[i].size(); j++){
                    if(vect[i][j].compare(vect[i][j].size() - 1, 1, "\r") == 0){
                        vect[i][j].erase(vect[i][j].size() - 1);
                    }
                }

                // We check if it's already in our database :
                stmt = con->createStatement();
                sql::SQLString checkDb;
                checkDb+= "SELECT id from project WHERE title = '";
                checkDb+= vect[i][0];
                checkDb+= "';";

                cout << checkDb<< endl;

                sql::ResultSet *resultSet3 = stmt->executeQuery(checkDb);

                if(resultSet3->next()){
                    // Already in the database :
                    cout << vect[i][0] << " : Already in database" << endl;
                } else {
                    cout << "here";
                    // We get the id of the thematic :
                    sql::ResultSet *resultSet;
                    stmt = con->createStatement();
                    sql::SQLString getDP;
                    getDP+="SELECT id FROM thematic WHERE name = '";
                    getDP+=vect[i][4];
                    getDP+= "' ;";

                    cout << getDP << endl;

                    resultSet = stmt->executeQuery(getDP);
                    resultSet->next();

                    int idThematic = stoi(resultSet->getString(1));

                    // We get the ID of the teacher
                    sql::ResultSet *resultSet2;
                    stmt = con->createStatement();
                    sql::SQLString getIdTeacher;
                    getIdTeacher+="SELECT id FROM staff WHERE last_name = '";
                    getIdTeacher+=vect[i][1];
                    getIdTeacher+= "' ;";
                    resultSet2 = stmt->executeQuery(getIdTeacher);
                    resultSet2->next();
                    int idTeacher = stoi(resultSet2->getString(1));

                    stmt = con->createStatement();
                    sql::SQLString req;
                    req+= "INSERT INTO project (id_thematic_id, id_teacher_id, title, description, technical_domains, is_taken, year)"
                          " VALUES ( ";
                    req+= to_string(idThematic);
                    req+= " , ";
                    req+= to_string(idTeacher);
                    req+= " , '";
                    req+= vect[i][0];
                    req+= "' , '";
                    req+= vect[i][2];
                    req+= "' , '";
                    req+= vect[i][3];
                    req+= "' , 0 , ";
                    req+= vect[i][6];
                    req+= " );";

                    cout << req << endl;
                    stmt->executeUpdate(req);

                    // First we take the id of the project :
                    stmt = con->createStatement();
                    sql::SQLString req3;
                    req3 += "SELECT id FROM project WHERE title = '";
                    req3 += vect[i][0];
                    req3 += "';";

                    sql::ResultSet *resultSet4;
                    resultSet4 = stmt->executeQuery(req3);

                    resultSet4->next();
                    int idProject = stoi(resultSet4->getString(1));

                    // Then we take the id of the professional domain :
                    vector<string> v = split (vect[i][5], ",");
                    for(int k = 0; k< v.size(); k++){
                        // We take the id of the project from the parsed line, and we put it in the mapping table :
                        stmt = con->createStatement();
                        sql::SQLString req4;
                        req4+= "SELECT id FROM professional_domain WHERE name = '";
                        req4+= v[k];
                        req4+= "';";

                        sql::ResultSet *resultSet5;
                        resultSet5 = stmt->executeQuery(req4);

                        resultSet5->next();
                        int idDp = stoi(resultSet5->getString(1));

                        sql::SQLString req5;

                        req5+= "INSERT INTO project_professional_domain (project_id, professional_domain_id)"
                              " VALUES ( ";
                        req5+= to_string(idProject);
                        req5+= " , ";
                        req5+= to_string(idDp);
                        req5+= " );";

                        cout << req5 << endl;

                        stmt->executeUpdate(req5);
                    }
                }
            }
        }catch (sql::SQLException &e){
            cout << "#ERR : SQLException in "<< __FILE__;
            cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
            cout << " ERR" <<e.what() << endl;
        }
    }
}

// Populate the proejectWishes Table from a CSV.
// We need to have the projectWishes Table empty to do this,
void populateProjectWishesTable(){
    std::filebuf fb;
    if (fb.open (POPULATEPROJECTWISHES,std::ios::in))
    {
        std::istream is(&fb);
        vector<vector<std::string>> vect = readCSV(is);
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        try {
            // Création of a new connexion :
            driver = get_driver_instance();
            con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);
            // Connexion to the database :
            con->setSchema("projisen");
            // First step : counting how much vertices we have
            for(int i = 1; i<vect.size(); i++){
                if(vect[i][0].compare(vect[i][0].size() - 1, 1, "\r") == 0){
                    vect[i][0].erase(vect[i][0].size() - 1);
                }

                //For each line, we search the student, and the 3 project :
                //Main student :
                stmt = con->createStatement();
                sql::SQLString searchMainStudent;
                searchMainStudent+= "SELECT id from student WHERE last_name ='";
                searchMainStudent+= vect[i][1];
                searchMainStudent+= "';";
                cout << searchMainStudent<< endl;
                sql::ResultSet *mainStudentResultSet = stmt->executeQuery(searchMainStudent);

                // Seconde student :
                stmt = con->createStatement();
                sql::SQLString searchSecondStudent;
                searchSecondStudent+= "SELECT id from student WHERE last_name ='";
                searchSecondStudent+= vect[i][2];
                searchSecondStudent+= "';";
                cout << searchSecondStudent<< endl;
                sql::ResultSet *secondStudentResultSet = stmt->executeQuery(searchSecondStudent);

                // Project 1 :
                stmt = con->createStatement();
                sql::SQLString searchProject1;
                searchProject1+= "SELECT id from project WHERE title ='";
                searchProject1+= vect[i][3];
                searchProject1+= "';";
                cout << searchProject1<< endl;
                sql::ResultSet *project1ResultSet = stmt->executeQuery(searchProject1);

                // Project 2 :
                stmt = con->createStatement();
                sql::SQLString searchProject2;
                searchProject2+= "SELECT id from project WHERE title ='";
                searchProject2+= vect[i][4];
                searchProject2+= "';";
                cout << searchProject2<< endl;
                sql::ResultSet *project2ResultSet = stmt->executeQuery(searchProject2);

                // Project 3 :
                stmt = con->createStatement();
                sql::SQLString searchProject3;
                searchProject3+= "SELECT id from project WHERE title ='";
                searchProject3+= vect[i][5];
                searchProject3+= "';";
                cout << searchProject3<< endl;
                sql::ResultSet *project3ResultSet = stmt->executeQuery(searchProject3);

                mainStudentResultSet->next();
                int idMainStudent = stoi(mainStudentResultSet->getString(1));
                int idSecondStudent = 0;
                bool secondStudent;
                if(secondStudentResultSet->next()){
                    idSecondStudent = stoi(secondStudentResultSet->getString(1));
                    secondStudent = true;
                } else {
                   secondStudent = false;
                }

                project1ResultSet->next();
                int idProject1 = stoi(project1ResultSet->getString(1));

                project2ResultSet->next();
                int idProject2 = stoi(project2ResultSet->getString(1));

                project3ResultSet->next();
                int idProject3 = stoi(project3ResultSet->getString(1));

                sql::SQLString insertReq;

                // Insertion into project Wishes
                insertReq+= "INSERT INTO project_wishes (id, id_project_1_id, id_project_2_id, id_project_3_id, id_main_student_id)"
                       " VALUES ( ";
                insertReq+= vect[i][0];
                insertReq+= " , ";
                insertReq+= to_string(idProject1);
                insertReq+= " , ";
                insertReq+= to_string(idProject2);
                insertReq+= " , ";
                insertReq+= to_string(idProject3);
                insertReq+= " , ";
                insertReq+= to_string(idMainStudent);
                insertReq+= " );";

                cout << insertReq << endl;

                stmt->executeUpdate(insertReq);

                // Update main Student :
                if(secondStudent) {
                    sql::SQLString updadeMainStudentReq;
                    updadeMainStudentReq += "UPDATE student SET id_pair_id = ";
                    updadeMainStudentReq += to_string(idSecondStudent);
                    updadeMainStudentReq += ", is_main_student = 1 WHERE id = ";
                    updadeMainStudentReq += to_string(idMainStudent);
                    cout << updadeMainStudentReq << endl;
                    stmt->executeUpdate(updadeMainStudentReq);

                    sql::SQLString updateSenconStudentReq;
                    updateSenconStudentReq += "UPDATE student SET id_pair_id = ";
                    updateSenconStudentReq += to_string(idMainStudent);
                    updateSenconStudentReq += ", is_main_student = 0 WHERE id = ";
                    updateSenconStudentReq += to_string(idSecondStudent);
                    cout << updateSenconStudentReq << endl;
                    stmt->executeUpdate(updateSenconStudentReq);


                } else {
                    sql::SQLString updadeMainStudentReq;
                    updadeMainStudentReq += "UPDATE student SET ";
                    updadeMainStudentReq += "is_main_student = 1 WHERE id = ";
                    updadeMainStudentReq += to_string(idMainStudent);
                    cout << updadeMainStudentReq << endl;
                    stmt->executeUpdate(updadeMainStudentReq);
                }
            }
        }catch (sql::SQLException &e){
            cout << "#ERR : SQLException in "<< __FILE__;
            cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
            cout << " ERR" <<e.what() << endl;
        }
    }

}


void populateTable(){
    // Function to populate all the table from the CSV files :
    // First, we do the thematic and the professional domain table :

    // Thematic table :
    populateThematicTable();

    // Professional Domain Table :
    populateDomainProTable();

    cout << "Staff" << endl;
    // Populate the teacher table :
    populateTeacherTable();

    cout << "student "<< endl;
    // Population of the student table :
    populateStudentTable();

    // Population of the project Table :
    populateProjectTable();

}


