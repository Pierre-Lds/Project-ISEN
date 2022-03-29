//
// Created by rouxt on 24/02/2022.
//
#include <iostream>
#include <vector>
#include <fstream>
#include <sstream>
#include "GraphEcart.h"
#include "global.h"

using namespace std;

GraphEcart::GraphEcart(int **newGraphEcartCost, int newNumVertices, int nbStudent) {
    this->graphEcartCost = newGraphEcartCost;
    this->numVertices = newNumVertices;
    this->nbStudent = nbStudent;

}

void GraphEcart::printGraphEcartCost() {
    if(boolPrint){
        cout << "printGraphEcart cost" << endl;
        cout << "  ";
        for(int i = 0; i< numVertices; i++){
            cout <<i << " ";
            if(i<10){
                cout << " ";
            }
        }

        cout << endl;
        for(int i = 0; i<numVertices; i++){

            cout << i << " ";
            if(i<10){
                cout << " ";
            }

            for(int j=0; j<numVertices; j++){
                if(j == nbStudent){
                    cout << graphEcartCost[i][j] << "| ";
                }
                else {
                    cout << graphEcartCost[i][j] << "  ";
                }
            }
            cout << endl;

            if(i == nbStudent){
                for (int j=0; j<numVertices; j++){
                    cout << "___";
                }
                cout << endl;
            }

        }
        cout << endl;
    }

}

int* GraphEcart::bellamFord() {

    if(boolPrint){
        cout << "Bellam ford function " << endl;
    }

    // Implementation of the bellam ford algorithm :

    // Step 1 - Initialisation

    // The source is 0;
    int source = 0;

    int distance[numVertices];
    int* predecessor = new int[numVertices];

    // We put the distance to this vertex to infinity,
    // There is no predecessor (-1)
    for(int i = 0; i<numVertices; i++){
        distance[i] = INT16_MAX;
        predecessor[i] = -1;
    }
    distance[source] = 0;

    // Step 2 :
    // We release the edges :
    // We repeat our action V-times :


    srand((unsigned) time(0));




    for(int k=0; k< numVertices*numVertices; k++){
        int result =(rand() % 4);
        if(result == 1){
            // We go one each edges :
            for(int u=0; u<numVertices; u++){
                for(int v=0; v<numVertices; v++){
                    // We check if the edge exist :
                    if(graphEcartCost[u][v] != 0){
                        if(distance[u] != INT16_MAX && distance[u] + graphEcartCost[u][v] < distance[v] ){
                            distance[v] = distance[u] + graphEcartCost[u][v];
                            predecessor[v] = u;
                        }
                    }
                }
            }
        } if(result == 2) {
            // We go one each edges :
            for(int u=numVertices-1; u>=0; u--){
                for(int v=numVertices-1; v>=0; v--){
                    // We check if the edge exist :
                    if(graphEcartCost[u][v] != 0){
                        if(distance[u] != INT16_MAX && distance[u] + graphEcartCost[u][v] < distance[v] ){
                            distance[v] = distance[u] + graphEcartCost[u][v];
                            predecessor[v] = u;
                        }
                    }
                }
            }
        } if(result == 3) {
            // We go one each edges :
            for (int u = 0; u < numVertices; u++) {
                for (int v = numVertices - 1; v >= 0; v--) {
                    // We check if the edge exist :
                    if (graphEcartCost[u][v] != 0) {
                        if (distance[u] != INT16_MAX && distance[u] + graphEcartCost[u][v] < distance[v]) {
                            distance[v] = distance[u] + graphEcartCost[u][v];
                            predecessor[v] = u;
                        }
                    }
                }
            }
        }else {
            // We go one each edges :
            for(int u=numVertices-1; u>=0; u--){
                for(int v=0-1; v< numVertices; v++){
                    // We check if the edge exist :
                    if(graphEcartCost[u][v] != 0){
                        if(distance[u] != INT16_MAX && distance[u] + graphEcartCost[u][v] < distance[v] ){
                            distance[v] = distance[u] + graphEcartCost[u][v];
                            predecessor[v] = u;
                        }
                    }
                }
            }
        }
    }

    for(int u=0; u<numVertices; u++){
        for(int v=0; v<numVertices; v++){
            if(graphEcartCost[u][v] != 0){
                if(distance[v] != INT16_MAX && distance[u] + graphEcartCost[u][v] < distance[v]){
                    cout << distance[u] << endl;
                    cout << distance[v] << endl;
                    cout << "u: " <<  u << endl;
                    cout << "v: " << v << endl;
                    cout << graphEcartCost[u][v] << endl;
                    cout << " Negative cycle :)" << endl;
                    EXIT_FAILURE;
                }
            }
        }
    }

    // Print to check :
    //for(int i=0; i<numVertices; i++){
    //    cout << " - " << i << " : " << distance[i] << endl;
    //}
    // We print the predecessor :
    if(boolPrint){
        int point = numVertices - 1;

        cout << "Predecessor - " << endl;
        cout << " - "<< numVertices - 1 << endl;
        while(predecessor[point] != -1){

            cout << " - " << predecessor[point] << endl;
            point = predecessor[point];

        }
    }

    return predecessor;
}

