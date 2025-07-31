<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Projects;

final class ProjectsController extends AbstractController
{
    /**
     * Retrieve all projects
     */

    #[Route('/api/projects', name: 'get_projects')]
    public function getProjects(EntityManagerInterface $entityManager): JsonResponse
    {
        $projects = $entityManager->getRepository(Projects::class)->findAll();
        return $this->json($projects, 200);
    }

    /**
     * Retrieve a specific project given by ID
     * If no project is found, return an error message
     */

    #[Route('/api/project/{id}', name: 'get_project_id')]
    public function getProjectById(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $project = $entityManager->getRepository(Projects::class)->find($id);

        if(!$project){
            return $this->json([
                'message' => 'Project not found'
            ]);
        }

        return $this->json($project, 200);
    }

    /**
     * Insert a new project
     */

    #[Route('/api/projects/insert', name: 'insert_project', methods: ['POST'])]
    public function insertProject(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project = new Projects();
        $project->setName($data['name']);
        $project->setDescription($data['description']);
        $project->setSkills($data['skills']);

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->json([
            'message' => 'Project added',
            'project' => $project
        ], 201);
    }

    /**
     * Update a specific project given by ID
     * If no project is found, return an error message
     */

    #[Route('/api/project/{id}/update', name: 'update_project', methods: ['PUT'])]
    public function updateProject(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $project = $entityManager->getRepository(Projects::class)->find($id);

        if(!$project){
            return $this->json([
                'message' => 'Project not found'
            ]);
        }

        $data = json_decode($request->getContent(), true);

        $project->setName($data['name']);
        $project->setDescription($data['description']);
        $project->setSkills($data['skills']);

        $entityManager->flush();

        return $this->json([
            'message' => 'Project updated',
            'project' => $project
        ], 201);
    }

    /**
     * Delete a specific project given by ID
     * If no project is found, return an error message
     */

    #[Route('/api/project/{id}/delete', name: 'delete_project', methods: ['DELETE'])]
    public function deleteProject(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $project = $entityManager->getRepository(Projects::class)->find($id);

        if(!$project){
            return $this->json([
                'message' => 'Project not found'
            ]);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json([
            'message' => 'Project deleted'
        ], 201);
    }
}
