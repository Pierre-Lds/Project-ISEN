//
// Created by rouxt on 24/02/2022.
//

#include <iostream>
using namespace std;
#ifndef ALGO_GRAPHECART_H
#define ALGO_GRAPHECART_H

// Create graph from a CSV, use to test the graph
int** createGraphFromCSV(string filename);


class GraphEcart {
private:
    // The two matrix that we will use

    int **graphEcartCost;
    // Other useful things
    int numVertices;
    int nbStudent;

public:
    // Constructor, set up the two graph
    GraphEcart(int **newGraphEcartCost, int newNumVertices, int nbSudent);

    // print function
    void printGraphEcartCost();

    // The bellamFord algorithme that we use to find the shortest path between the source and the well,
    // We use this algorithm on the cost matrix
    int *bellamFord();

};
#endif //ALGO_GRAPHECART_H
