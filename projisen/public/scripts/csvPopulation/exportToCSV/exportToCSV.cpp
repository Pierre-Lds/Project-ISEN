//
// Created by rouxt on 04/03/2022.
//

#include <cppconn/driver.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <vector>
#include <fstream>
#include "exportToCSV.h"


using namespace std;
using namespace sql;

#include "../define.h"

void writeCSV(string fileName, std::vector<std::pair<std::string, std::vector<string>>> dataset){

    ofstream myFfile(fileName);

    // Column names :
    for(int j=0; j < dataset.size(); j++){
        myFfile << dataset.at(j).first;
        if(j != dataset.size() - 1) {
            myFfile << ",";
        }
    }
    myFfile << "\n";

    // Data :
    for(int i = 0; i<dataset.at(0).second.size(); i++){
        for(int j=0; j<dataset.size(); j++){
            myFfile << dataset.at(j).second.at(i);
            if( j!= dataset.size() - 1){
                myFfile << ",";
            }
        }
        myFfile << "\n";
    }


}

void exportProjectToCSV(){

    try {
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        sql::ResultSet *res;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);

        // Connexion to the database :
        con->setSchema("projetM1DataBase");

        // First step : counting how much vertices we have
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM project as _message");

        vector<string> vectorID;
        vector<string> vectorIDThematic;
        vector<string> vectorIDTeacher;
        vector<string> vectorTitle;
        vector<string> vectorDesc;
        vector<string> vectorTechnicalDomains;
        vector<string> vectorIsTaken;
        vector<string> vectorYear;

        while (res->next()){
            vectorID.push_back(res->getString(1));
            vectorIDThematic.push_back(res->getString(2));
            vectorIDTeacher.push_back(res->getString(3));
            vectorTitle.push_back(res->getString(4));
            vectorDesc.push_back(res->getString(5));
            vectorTechnicalDomains.push_back(res->getString(6));
            vectorIsTaken.push_back(res->getString(7));
            vectorYear.push_back(res->getString(8));

        }

        std::vector<std::pair<std::string, std::vector<string>>> vals = {{"id", vectorID},{"idThematic", vectorIDThematic},
                                                                         {"idTeacher", vectorIDTeacher}, {"title", vectorTitle},
                                                                         {"descriptions", vectorDesc}, {"technicalDomain", vectorTechnicalDomains},
                                                                         {"isTaken", vectorIsTaken}, {"year", vectorYear}};
        writeCSV(projectFile, vals);

    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
    }
}


void exportProffesionalDomainToCSV(){
    try {
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        sql::ResultSet *res;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);

        // Connexion to the database :
        con->setSchema("projetM1DataBase");

        // First step : counting how much vertices we have
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM professional_domain as _message");

        vector<string> vectorID;
        vector<string> vectorName;

        while (res->next()){
            vectorID.push_back(res->getString(1));
            vectorName.push_back(res->getString(2));
        }

        std::vector<std::pair<std::string, std::vector<string>>> vals = {{"id", vectorID},{"name", vectorName}};
        writeCSV(domaineProFile, vals);

    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
    }
}


void exportThematicToCSV(){

    try {
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        sql::ResultSet *res;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);

        // Connexion to the database :
        con->setSchema("projetM1DataBase");

        // First step : counting how much vertices we have
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM thematic as _message");

        vector<string> vectorID;
        vector<string> vectorName;

        while (res->next()){
            vectorID.push_back(res->getString(1));
            vectorName.push_back(res->getString(2));
        }

        std::vector<std::pair<std::string, std::vector<string>>> vals = {{"id", vectorID},{"name", vectorName}};
        writeCSV(thematicFile, vals);

    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
    }
}



void exportStaffToCSV(){

    try {
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        sql::ResultSet *res;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);

        // Connexion to the database :
        con->setSchema("projetM1DataBase");

        // First step : counting how much vertices we have
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM staff as _message");

        vector<string> vectorID;
        vector<string> vectorUserName;
        vector<string> vectorRole;
        vector<string> vectorFirstName;
        vector<string> vectorLastName;
        vector<string> vectorPwdHash;
        vector<string> vectorIsAdmin;
        while (res->next()){
            vectorID.push_back(res->getString(1));
            vectorUserName.push_back(res->getString(2));
            vectorRole.push_back(res->getString(3));
            vectorFirstName.push_back(res->getString(4));
            vectorLastName.push_back(res->getString(5));
            vectorPwdHash.push_back(res->getString(6));
            vectorIsAdmin.push_back(res->getString(7));
        }

        std::vector<std::pair<std::string, std::vector<string>>> vals = {{"id", vectorID}, {"first_name", vectorFirstName}, {"roles", vectorRole} , {"last_name", vectorLastName}, {"pwd_hash", vectorPwdHash}, {"User_name", vectorUserName}};
        writeCSV(staffFile, vals);

    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
    }
}


void exportStudentToCSV(){

    try {
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        sql::ResultSet *res;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", USERNAME, PASSWORD);

        // Connexion to the database :
        con->setSchema("projetM1DataBase");

        // First step : counting how much vertices we have
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM student as _message");

        vector<string> vectorID;
        vector<string> vectorIDPair;
        vector<string> vectorUsername;
        vector<string> vectorRole;
        vector<string> vectorPwdHash;
        vector<string> vectorFirstName;
        vector<string> vectorLastName;
        vector<string> vectorIsMainStudent;
        vector<string> vectorGraduationYear;
        vector<string> vectorProfessionalDomain;
        vector<string> vectorIdProject;

        while (res->next()){
            vectorID.push_back(res->getString(1));
            vectorIDPair.push_back(res->getString(2));
            vectorUsername.push_back(res->getString(3));
            vectorRole.push_back(res->getString(4));
            vectorPwdHash.push_back(res->getString(5));
            vectorFirstName.push_back(res->getString(6));
            vectorLastName.push_back(res->getString(7));
            vectorIsMainStudent.push_back(res->getString(8));
            vectorGraduationYear.push_back(res->getString(9));
            vectorProfessionalDomain.push_back(res->getString(10));
            vectorIdProject.push_back(res->getString(11));
        }

        std::vector<std::pair<std::string, std::vector<string>>> vals = {{"id", vectorID}, {"idPair", vectorIDPair},
                                                                         {"Username", vectorUsername}, {"Role", vectorRole},
                                                                         {"PasswordHash", vectorPwdHash}, {"FirstName", vectorFirstName},
                                                                         {"last_name", vectorLastName}, {"isMainStudent", vectorIsMainStudent},
                                                                         {"GraduationYear", vectorGraduationYear}, {"DomainPro", vectorProfessionalDomain},
                                                                         {"id_project", vectorIdProject}};
        writeCSV(studentFile, vals);

    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
    }

}
