//
// Created by rouxt on 21/02/2022.
//

#include "Graph.h"
#include "GraphEcart.h"
#include "global.h"
#include <stdlib.h>
#include <iostream>
#include <mysql_connection.h>
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <vector>

#include <istream>
#include <fstream>
#include <cstring>
#include <sstream>

using namespace sql;
using namespace std;

Graph::Graph() {

}

void Graph::setUpMatrix() {
    if(boolPrint){
        cout << "Graph::setUpMatrix" << endl;
    }

    try{
        sql::Driver *driver;
        sql::Connection *con;
        sql::Statement *stmt;
        sql::ResultSet *res;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", "root", "root");

        // Connexion to the database :
        con->setSchema("projisen");

        // First step : counting how much vertices we have
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM project_wishes as _message");

        int num = 0;
        // Four new vertices for each row of the table, + the source + the well
        while (res->next()){
            num++;
        }

        // Fourth step : we need to keep a track of all the data, to not lose it when we put our things in the
        // We put everything in the save tab,
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM project_wishes as _message");


        // To stock all of the ids
        int idMinStudent = INT32_MAX;
        int idMaxStudent = 0;
        int idMinProject = INT32_MAX;
        int idMaxProject = 0;
        while(res->next()){
            if(stoi(res->getString(1)) < idMinStudent){
                idMinStudent = stoi(res->getString(1));
            }
            if(stoi(res->getString(1)) > idMaxStudent){
                idMaxStudent = stoi(res->getString(1));
            }
            if(stoi(res->getString(2)) < idMinProject){
                idMinProject = stoi(res->getString(2));
            }
            if(stoi(res->getString(3)) < idMinProject){
                idMinProject = stoi(res->getString(3));
            }
            if(stoi(res->getString(4)) < idMinProject){
                idMinProject = stoi(res->getString(4));
            }
            if(stoi(res->getString(2)) > idMaxProject){
                idMaxProject = stoi(res->getString(2));
            }
            if(stoi(res->getString(3)) > idMaxProject){
                idMaxProject = stoi(res->getString(3));
            }
            if(stoi(res->getString(4)) > idMaxProject){
                idMaxProject = stoi(res->getString(4));
            }
        }

        this->nbStudent =  idMaxStudent - idMinStudent + 1;

        if(boolPrint){
            cout << "idMinStudent : " << idMinStudent << endl;
            cout << "idMaxStudent : " << idMaxStudent << endl;
            cout << "idMinProject : " << idMinProject << endl;
            cout << "idMaxProject : " << idMaxProject << endl;
        }

        this->numVertices = idMaxStudent + idMaxProject - idMinProject +3;

        this->save = new int[numVertices];

        // Second step : Creation of the 3 matrix :
        capacityMatrix = new int*[numVertices];
        costMatrix = new int*[numVertices];
        flowMatrix = new int*[numVertices];

        for(int i = 0; i<numVertices; i++){
            capacityMatrix[i] = new int[numVertices];
            costMatrix[i] = new int[numVertices];
            flowMatrix[i] = new int[numVertices];
        }

        // Third step : Setting up the flow matrix :
        // It's only 0 since there are no flow at start

        for(int i = 0; i<numVertices; i++){
            for(int j = 0; j<numVertices; j++){
                flowMatrix[i][j] = 0;
                capacityMatrix[i][j] = 0;
                costMatrix[i][j] = 0;
            }
        }

        // We set up everything inside the save table :
        save[0] = 0;    // Root
        int idSave = 0;
        for(int i = idMinStudent; i<=idMaxStudent; i++){
            save[idSave + 1] = idMinStudent + idSave;
            idSave++;
        }
        for(int i = idMinProject; i<=idMaxProject; i++){
            save[idSave + 1] = idMinProject + idSave - idMaxStudent;
            idSave++;
        }

        // Print of the table save :
        // Remove the comment to test the table :
        cout << "Save table : " << endl;
        for(int i = 0; i<numVertices; i++){
           cout << i << " : " << save[i] << endl;
        }
        cout << endl;



        // Final step : Setting up the rest of the matrice  :
        // Sources -> connexion with all the students (first column of the answer)
        // All the studients -> theirs project (Column 2, 3 and 4 of the request)
        // All the projects -> the well

        // Connect the source to all the students
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM project_wishes as _message");

        while (res->next()){
            // Count the number of iteration


            int verNum = stoi(res->getString(1));
            int numProj1 = stoi(res->getString(2));
            int numProj2 = stoi(res->getString(3));
            int numProj3 = stoi(res->getString(4));

            int saveStudent;

            // We put the student inside the graph
            for(int i = 0; i<= nbStudent; i++){
                if(verNum == save[i]){
                    saveStudent = i;
                    addEdge(0, i, 5, 1);
                }
            }


            // We connect the student to his project : , and the project to the well

            for(int i = nbStudent; i<= numVertices; i++){
                if(numProj1 == save[i]){
                        addEdge(saveStudent, i, 1, 1);
                        addEdge(i, numVertices-1, 4, 1);
                    }
                    if(numProj2 == save[i]){
                        addEdge(saveStudent, i, 2, 1);
                        addEdge(i, numVertices-1, 4, 1);
                    }
                    if(numProj3 == save[i]){
                        addEdge(saveStudent, i, 3, 1);
                        addEdge(i, numVertices-1, 4, 1);
                    }
                }

            }
        con->close();
        // Test :
        printCostMatrix();
        printCapacityMatrix();
        //printFlowMatrix();
    } catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
    }
}

void Graph::addEdge(int departure, int arrival, int cost, int capacity) {
    capacityMatrix[departure][arrival] = capacity;
    costMatrix[departure][arrival] = cost;
}

void Graph::printFlowMatrix() {
    if(boolPrint) {
        cout << "printFlowMatrix" << endl;


        cout << "  ";
        for (int i = 0; i < numVertices; i++) {
            cout << i << " ";
            if (i < 10) {
                cout << " ";
            }
        }
        cout << endl;
        for (int i = 0; i < numVertices; i++) {

            cout << i << " ";
            if (i < 10) {
                cout << " ";
            }

            for (int j = 0; j < numVertices; j++) {
                if (j == nbStudent) {
                    cout << flowMatrix[i][j] << "| ";

                } else {
                    cout << flowMatrix[i][j] << "  ";
                }
            }
            cout << endl;

            if (i == nbStudent) {
                for (int j = 0; j < numVertices; j++) {
                    cout << "___";
                }
                cout << endl;
            }

        }
        cout << endl;
    }
}

void Graph::printCapacityMatrix() {
    if(boolPrint) {


        cout << "printCapacityMatrix" << endl;
        cout << "  ";
        for (int i = 0; i < numVertices; i++) {
            cout << i << " ";
            if (i < 10) {
                cout << " ";
            }
        }
        cout << endl;
        for (int i = 0; i < numVertices; i++) {

            cout << i << " ";
            if (i < 10) {
                cout << " ";
            }

            for (int j = 0; j < numVertices; j++) {
                if (j == nbStudent) {
                    cout << capacityMatrix[i][j] << "| ";

                } else {
                    cout << capacityMatrix[i][j] << "  ";
                }
            }
            cout << endl;

            if (i == nbStudent) {
                for (int j = 0; j < numVertices; j++) {
                    cout << "___";
                }
                cout << endl;
            }

        }
        cout << endl;
    }
}

void Graph::printCostMatrix() {
    if(boolPrint) {


        cout << "printCostMatrix" << endl;
        cout << "  ";
        for (int i = 0; i < numVertices; i++) {
            cout << i << " ";
            if (i < 10) {
                cout << " ";
            }
        }
        cout << endl;
        for (int i = 0; i < numVertices; i++) {

            cout << i << " ";
            if (i < 10) {
                cout << " ";
            }

            for (int j = 0; j < numVertices; j++) {
                if (j == nbStudent) {
                    cout << costMatrix[i][j] << "| ";

                } else {
                    cout << costMatrix[i][j] << "  ";
                }
            }
            cout << endl;

            if (i == nbStudent) {
                for (int j = 0; j < numVertices; j++) {
                    cout << "___";
                }
                cout << endl;
            }

        }
        cout << endl;
    }
}

int **Graph::getCapacityMatrix(){
    return capacityMatrix;
}

int **Graph::getCostMatrix(){
    return costMatrix;
}

int **Graph::getFlowMatrix(){
    return flowMatrix;
}

int Graph::getNumVertices() {
    return numVertices;
}

int *Graph::getSave() {
    return save;
}

void Graph::printSave() {
    for(int i=0; i<numVertices; i++){

    }
}

void Graph::removeEdge(int departure, int arrival) {
    capacityMatrix[departure][arrival] = 0;
    costMatrix[departure][arrival] = 0;
    flowMatrix[departure][arrival] = 0;
}



void Graph::busackerAndGowen() {
    if(boolPrint){
        cout << "Busacker and Gowen Algorithm : " << endl;
    }

    // Var that we use to know if the Bellman-Ford algorithm can find something else or no :)
    bool quit = false;

    int i = 1;

    while(!quit){
        // To print the percent and the current iteration of the code,
        //
        float percent = (float)i/nbStudent * 100;
        if(boolPrint){
            cout << "Itération : " << i <<" - Pourcentage : " <<  percent << endl;
        }
        i++;

        GraphEcart* graphEcart = new GraphEcart(buildCostEcartFlow(), this->numVertices, this->nbStudent);
        //graphEcart->printGraphEcartCost();

        // We get the predecessor thanks to the bellam ford function
        int* pred = graphEcart->bellamFord();

        // We check if we can continue :
        if(pred[numVertices - 1] == -1){
            // If we go here that means there is no more possibilities :
            quit = true;
        }

        // Then we need to change our flow graph depending of the return of pred :
        // Because we have a capacity of one on every vertex, we can put the flow to one :
        if(!quit){
            int point = numVertices -1;

            while(pred[point] != -1){
                // If we already have a flow, we remove it :
                for(int f=0; f< numVertices; f++){
                    if(flowMatrix[pred[point]][f] == 1 && point > nbStudent){
                        flowMatrix[pred[point]][f] = 0;
                    }
                }

                // Basic case :
                if(pred[point] > point){
                    flowMatrix[pred[point]][point] = 0;
                } else {
                    flowMatrix[pred[point]][point] = 1;
                }
                point = pred[point];
            }
        }
    }

    // Finally, we can get the response with the save table and show wich student is with wich project :
    // To do that, we check the flow matrix :

    //printFlowMatrix();



    //while(pred2[point] != -1){
    //    cout << pred2[point] << " -- " << point << endl;
    //    flowMatrix[pred2[point]][point] = 1;
    //    point = pred2[point];
    //}
}

int **Graph::buildCostEcartFlow() {
    // Matrix to return :
    int** ecartMatrix = new int*[numVertices];
    for(int i = 0; i<numVertices; i++){
        ecartMatrix[i] = new int[numVertices];
    }

    // We initialize the matrix to 0 :
    for(int i = 0; i < numVertices; i++){
        for(int j=0; j<numVertices; j++){
            ecartMatrix[i][j] = 0;
        }
    }

    // Then, we build the matrix :
    for(int i = 0; i < numVertices; i++){
        for(int j=0; j<numVertices; j++){
            if(flowMatrix[i][j] == capacityMatrix[i][j] && flowMatrix[i][j] != 0){
                // This arc is full, so we put it in the other sens and with a negative value :
                ecartMatrix[j][i] = -costMatrix[i][j];
                //cout << costMatrix[i][j] << " - " << endl;
                //cout << ecartMatrix[i][j] << " - ";
            }
            else {
                if(ecartMatrix[i][j] == 0){
                    ecartMatrix[i][j] = costMatrix[i][j];

                }

            }
        }
    }

    // To print the matrix in debug
    //for(int i = 0; i< numVertices; i++){
    //    for(int j=0; j<numVertices; j++){
    //        cprojetM1DataBaseout << ecartMatrix[i][j];
    //    }
    //    cout << endl;
    //}
    //cout<< endl;


    return ecartMatrix;
}

void Graph::printResult() {
    if(boolPrint){
        // First we print the result without the mapping table :
        for(int i=1; i<=nbStudent; i++){
            cout << "Student n° : " << i << " - ";
            for(int j=0; j<numVertices; j++){
                // We search the project of the student :
                if(flowMatrix[i][j] == 1){
                    cout << "Project n° : " << save[j];
                }
            }
            cout << endl;

        }
    }

}

void Graph::putResultInDatabase() {
    if(boolPrint){
        cout << "putResultInDatabase Function" << endl;
    }

    try {

        // Connexion to the database :
        for(int i=1; i<=nbStudent; i++){

            for(int j=0; j<numVertices; j++){
                // We search the project of the student :
                if(flowMatrix[i][j] == 1){
                    if(boolPrint){
                        cout << "Student n° : " << i << " - ";
                    }
                    sql::Driver *driver;
                    //sql::Connection *con;
                    sql::Statement *stmt;
                    sql::ResultSet *res;
                    sql::Connection *con;

                    // Création of a new connexion :
                    driver = get_driver_instance();
                    con = driver->connect("tcp://127.0.0.1:3306", "root", "root");
                    con->setSchema("projisen");

                    // We set the project as is_taken : 1
                    stmt = con->createStatement();
                    SQLString reqProject = "UPDATE project SET is_taken = 1 WHERE id = ";
                    reqProject+= (to_string(save[j]));
                    reqProject+= " ;";
                    stmt->execute(reqProject);


                    stmt = con->createStatement();
                    SQLString req = "UPDATE student SET id_project_id = ";
                    req.append(to_string(save[j]));
                    req.append(" WHERE (SELECT id_main_student_id FROM project_wishes WHERE id = ");
                    req.append(to_string(i));
                    req.append(") = id");
                    req.append(";");
                    if(boolPrint){
                        cout << req;
                    }
                    stmt->execute(req);
                    con->close();

                }
            }
            if(boolPrint){
                cout << endl;
            }

        }

    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
        cout << " ERR" << e.getSQLStateCStr() << endl;

    }
}
std::vector<std::string> getNextLineAndSplitIntoTokens(string str)   {
    std::vector<std::string> result;
    std::string line;
    std::string cell;

    char arr[str.length() + 1];
    strcpy(arr, str.c_str());

    ifstream istream1(arr);

    while(std::getline(istream1,cell))
    {
        result.push_back(cell);
    }

    return result;
}

// Conversion from string to int bcs stoi bug :)
int myStoi(string s) {
    // Initialize a variable
    int num = 0;
    int n = s.length();

    // Iterate till length of the string
    for (int i = 0; i < n; i++){
        // We remove everything outside the number
        if (s[i] >= 48 && s[i] < 58) {
            // Subtract 48 from the current digit
            num = num * 10 + (int(s[i]) - 48);
        }
    }

    // Print the answer
    return num;
}

void Graph::setUpMatrixFromCSV(string str) {

    vector<std::string> text = getNextLineAndSplitIntoTokens(str);
    // First we take the min student id, the max student id, the mix project id and the max project id

    int idMinStudent = INT32_MAX;
    int idMaxStudent = 0;
    int idMinProject = INT32_MAX;
    int idMaxProject = 0;


    for (auto i = text.begin(); i != text.end(); i++) {

        // Delimitor to parse the string :
        string delimiter = ";";

        // Id of the student / binome :
        string idStudent = i[0].substr(0, i[0].find(delimiter));

        // id of the first project :
        i[0].erase(0, i[0].find(delimiter) + delimiter.length());
        string idProject1 = i[0].substr(0, i[0].find(delimiter));

        // Id of the second project :
        i[0].erase(0, i[0].find(delimiter) + delimiter.length());
        string idProject2 = i[0].substr(0, i[0].find(delimiter));

        // Id of the third project :
        i[0].erase(0, i[0].find(delimiter) + delimiter.length());
        string idProject3 = i[0].substr(0, i[0].find(delimiter));

        int intIdStudent = myStoi(idStudent);
        int intIdProject1 = myStoi(idProject1);
        int intIdProject2 = myStoi(idProject2);
        int intIdProject3 = myStoi(idProject3);


        if(intIdStudent < idMinStudent){

            idMinStudent = intIdStudent;
        }
        if(intIdStudent > idMaxStudent){
            idMaxStudent = intIdStudent;
        }
        if(intIdProject1 < idMinProject){
            idMinProject = intIdProject1;
        }
        if(intIdProject2 < idMinProject){
            idMinProject = intIdProject2;
        }
        if(intIdProject3 < idMinProject){
            idMinProject =intIdProject3;
        }
        if(intIdProject1 > idMaxProject){
            idMaxProject = intIdProject1;
        }
        if(intIdProject2 > idMaxProject){
            idMaxProject = intIdProject2;
        }
        if(intIdProject3 > idMaxProject){
            idMaxProject = intIdProject3;
        }

        cout << "Binome : " << intIdStudent << " Project 1 - " << intIdProject1;
        cout << " Project 2 - " << intIdProject2;
        cout << " Project 3 - " << intIdProject3;
        cout << endl;
    }

    this->nbStudent = idMaxStudent - idMinStudent +1;
    cout << "idMinStudent : " << idMinStudent << endl;
    cout << "idMaxStudent : " << idMaxStudent << endl;
    cout << "idMinProject : " << idMinProject << endl;
    cout << "idMaxProject : " << idMaxProject << endl;

    // = the number of student + number of project (idMaxProject + idMinProject +1) + source + well
    this->numVertices = idMaxStudent + idMaxProject - idMinProject +3;

    this->save = new int[numVertices];

    // Creation of our matrix :
    capacityMatrix = new int*[numVertices];
    costMatrix = new int*[numVertices];
    flowMatrix = new int*[numVertices];

    for(int i = 0; i<numVertices; i++){
        capacityMatrix[i] = new int[numVertices];
        costMatrix[i] = new int[numVertices];
        flowMatrix[i] = new int[numVertices];
    }

    // Flow matrix, only 0 at the start :
    for(int i = 0; i<numVertices; i++){
        for(int j = 0; j<numVertices; j++){
            flowMatrix[i][j] = 0;
            capacityMatrix[i][j] = 0;
            costMatrix[i][j] = 0;
        }
    }

    // Set up the save :
    save[0] = 0;    // Root
    int idSave = 0;
    for(int i = idMinStudent; i<=idMaxStudent; i++){
        save[idSave + 1] = idMinStudent + idSave;
        idSave++;
    }
    for(int i = idMinProject; i<=idMaxProject; i++){
        save[idSave + 1] = idMinProject + idSave - idMaxStudent;
        idSave++;
    }

    cout << "Save table : " << endl;
    for(int i = 0; i<numVertices; i++){
        cout << i << " : " << save[i] << endl;
    }
    cout << endl;
    // Final step : Setting up the rest of the matrice  :
    // Sources -> connexion with all the students (first column of the answer)
    // All the studients -> theirs project (Column 2, 3 and 4 of the request)
    // All the projects -> the well
    text = getNextLineAndSplitIntoTokens(str);
    for (auto i = text.begin(); i != text.end(); i++) {

        // Delimitor to parse the string :
        string delimiter = ";";

        // Id of the student / binome :
        string idStudent = i[0].substr(0, i[0].find(delimiter));

        // id of the first project :
        i[0].erase(0, i[0].find(delimiter) + delimiter.length());
        string idProject1 = i[0].substr(0, i[0].find(delimiter));

        // Id of the second project :
        i[0].erase(0, i[0].find(delimiter) + delimiter.length());
        string idProject2 = i[0].substr(0, i[0].find(delimiter));

        // Id of the third project :
        i[0].erase(0, i[0].find(delimiter) + delimiter.length());
        string idProject3 = i[0].substr(0, i[0].find(delimiter));


        int intIdStudent = myStoi(idStudent);
        int intIdProject1 = myStoi(idProject1);
        int intIdProject2 = myStoi(idProject2);
        int intIdProject3 = myStoi(idProject3);
        cout << "Binome : " << intIdStudent << " Project 1 - " << intIdProject1;
        cout << " Project 2 - " << intIdProject2;
        cout << " Project 3 - " << intIdProject3;
        cout << endl;
        int saveStudent;
        // We put the student inside the graph
        for(int k = 0; k<= numVertices; k++){
            if(intIdStudent == save[k]){
                //cout << intIdStudent << "---" << k << endl;
                saveStudent = k;
                addEdge(0, k, 5, 1);
            }
        }
        // We connect the student to his project : , and the project to the well

        for(int k = 0; k<= numVertices; k++){
            if(intIdProject1 == save[k]){
                addEdge(saveStudent, k, 1, 1);
                addEdge(k, numVertices-1, 4, 1);
            }
            if(intIdProject2 == save[k]){
                addEdge(saveStudent, k, 2, 1);
                addEdge(k, numVertices-1, 4, 1);
            }
            if(intIdProject3 == save[k]){
                addEdge(saveStudent, k, 3, 1);
                addEdge(k, numVertices-1, 4, 1);
            }
        }
    }
    printCostMatrix();
    printCapacityMatrix();
}

#include <ctime>

int getYear(){
    time_t ttime = time(0);
    tm *local_time = localtime(&ttime);
    return 1900 + local_time->tm_year - 1;
}

void Graph::clearDatabase() {

    try{
        sql::Driver *driver;
        sql::Statement *stmt;
        sql::ResultSet *res;
        sql::Connection *con;

        // Création of a new connexion :
        driver = get_driver_instance();
        con = driver->connect("tcp://127.0.0.1:3306", "root", "root");
        con->setSchema("projisen");

        // Put everything in the project_wishes_legacy :
        stmt = con->createStatement();
        res = stmt->executeQuery("SELECT * FROM project_wishes as _message");

        int year = getYear();

        int idMaxProject = 0;
        while(res->next()){
            int idProject1 = stoi(res->getString(2));
            int idProject2 = stoi(res->getString(3));
            int idProject3 = stoi(res->getString(4));
            int idStudent = stoi(res->getString(5));

            SQLString insertRequest;
            insertRequest+= "INSERT INTO project_wishes_legacy (id_project_1, id_project_2, id_project_3, year, id_student) VALUES (";
            insertRequest+= to_string(idProject1);
            insertRequest+= ", ";
            insertRequest+= to_string(idProject2);
            insertRequest+= ", ";
            insertRequest+= to_string(idProject3);
            insertRequest+= ", ";
            insertRequest+= to_string(year);
            insertRequest+= ", ";
            insertRequest+= to_string(idStudent);
            insertRequest+= "); ";
            stmt = con->createStatement();
            cout << insertRequest << endl;
            stmt->executeUpdate(insertRequest);
        }

        // Delete the projectWishes table :
        stmt = con->createStatement();
        SQLString req = "DELETE FROM project_wishes;";
        stmt->execute(req);

        // Putting back the autoIncrement to 1 :
        stmt = con->createStatement();
        SQLString autoIncrReq ="ALTER TABLE project_wishes AUTO_INCREMENT = 1;";
        stmt->executeUpdate(autoIncrReq);
    }
    catch (sql::SQLException &e){
        cout << "#ERR : SQLException in "<< __FILE__;
        cout << "(" << __FUNCTION__ << ") on line " << __LINE__<< endl;
        cout << " ERR" <<e.what() << endl;
        cout << " ERR" << e.getSQLStateCStr() << endl;

    }

}



