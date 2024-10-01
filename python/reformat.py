import json
import os

# Read data form json file
def read_json_file(file_path):
    with open(file_path, 'r') as file:
        data = json.load(file)
    return data

def main(camera_pose_json_file_path, image_metadata_json_file_path, file_name):
    camera_pose = read_json_file(camera_pose_json_file_path)
    image_metadata = read_json_file(image_metadata_json_file_path)
    camera_pose_keys = camera_pose.keys()
    image_metadata_keys = image_metadata.keys()
    
    # merge two json files into one with the same key 
    for key in camera_pose_keys:
        if key in image_metadata_keys:
            camera_pose[key].update(image_metadata[key])
        else:
            print(key)
    
    # write the merged json file
    with open('./dsc/' + file_name + '_image_metadata.json', 'w') as file:
        json.dump(camera_pose, file, indent=4)
    
    return 1

main('./src/HAN-0212_20240921_camera_pose.json', './src/HAN-0212_20240921_image_metadata.json', 'HAN-0212_20240921')
main('./src/HAN-0240_20240118_camera_pose.json', './src/HAN-0240_20240118_image_metadata.json', 'HAN-0240_20240118')
main('./src/HNI-4067_20240124_camera_pose.json', './src/HNI-4067_20240124_image_metadata.json', 'HNI-4067_20240124')