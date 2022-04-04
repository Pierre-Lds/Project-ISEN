//
// Created by rouxt on 21/02/2022.
//

#ifndef ALGO_GRAPH_H
#define ALGO_GRAPH_H

#include <istream>

using namespace std;

class Graph {

private:
    // Matrix with the capacity of each edge
    int** capacityMatrix;

    // Matrix with the cost
    int** costMatrix;

    // Flow
    int** flowMatrix;

    // Number of vertices
    int numVertices;

    // To save the basic coordonate of our data
    int* save;

    // Save the number of student, we can find the number of project with that and the number of vertices
    int nbStudent;


public:
    // Constructor,
    Graph();

    // Set up the matrix with the data from database
    void setUpMatrix();

    // Set up the matrix from a CSV :
    void setUpMatrixFromCSV(string str);


    // Add an edge to the matrix or modify an edge
    void addEdge(int departure, int arrival, int cost, int capacity);

    // Remove an edge from the matrix
    void removeEdge(int departure, int arrival);

    // Print function for the 3 matrix and the debug
    void printFlowMatrix();
    void printCostMatrix();
    void printCapacityMatrix();

    // Print for the save table :
    void printSave();

    // Getters for the differents matrix in case we need them outside :
    int **getCapacityMatrix() ;
    int **getCostMatrix();
    int **getFlowMatrix();

    int getNumVertices();
    int *getSave();

    // Function for the Busacker and Gowen algorithm :
    // Lunch the algorithm and fill the flow matrix
    void busackerAndGowen();

    // Function to build the difference graph with the flow,
    // We will past this graph into the class GraphEcart
    int** buildCostEcartFlow();

    // Print the result from the flow matrix :
    void printResult();

    // Final function, we put the result into the database so that the PHP will make his treatement;
    void putResultInDatabase();

    // Empty the project_wishes table,
    // Fill the projectWishes legacy table
    void clearDatabase();

};


#endif //ALGO_GRAPH_H
